<?php

namespace backend\services\Session;

/**
 * Redis Session implements a session component using [redis](http://redis.io/) as the storage medium.
 *
 * Redis Session requires redis version 3.0.2 or higher to work properly.
 *
 * It needs to be configured with a redis [[Connection]] that is also configured as an application component.
 * By default it will use the `redis` application component.
 *
 * To use redis Session as the session application component, configure the application as follows,
 *
 * ~~~
 * [
 *     'components' => [
 *         'session' => [
 *             'class' => 'bscheshirwork\redis\Session',
 *             // 'redis' => 'redis' // id of the connection application component
 *         ],
 *     ],
 * ]
 * ~~~
 *
 * @property String $keyPrefix is a prefix for session key in Redis storage. Separate session by Yii::$app->id (as default).
 * @property String $userKeySeparator a separator for build user key. Default ':u:'
 * @property String $sessionKeySeparator a separator for build session key. Default ':s:'
 *
 * @author Bogdan Stepanenko <bscheshirwork@gmail.com>
 */
class Session extends \yii\redis\Session
{
    /**
     * @var string key separator for create user-dependence key
     */
    public $userKeySeparator = ':u:';

    /**
     * @var string key separator for create session-dependence key
     */
    public $sessionKeySeparator = ':s:';

    /**
     * Write session; Also store session id and user identity id
     * @inheritdoc
     * @param string $id
     * @param string $data
     * @return bool
     */
    public function writeSession($id, $data)
    {
        
        $result = parent::writeSession($id, $data);
        if ($result && ($userIdentity = \Yii::$app->user->getIdentity(false) ?? false)) {

            $userIdentityId = $userIdentity->getId() ?? 0;
            if(count($this->getSessionsById($userIdentityId)) > 1) return;
            $key = $this->keyUser($userIdentityId);
            $result = (bool) $this->redis->zadd($key, 'CH', $this->time(), $id);

            $result = $result && (bool) $this->redis->set($this->keySession($id), $userIdentityId);
        }

        return $result;
    }

    /**
     * Destroy session; Also remove session id from sorted set and user identity id
     * @inheritdoc
     * @param string $id
     * @return bool
     */
    public function destroySession($id): bool
    {
        parent::destroySession($id);
        if ($userIdentityId = (string) $this->redis->get($this->keySession($id))) {
            $this->redis->zrem($this->keyUser($userIdentityId), $id);
        }
        $this->redis->del($this->keySession($id));

        return true;
    }

    /**
     * Destroy all of user's sessions
     * force logout on all user devices
     * @param string $userIdentityId
     * @return bool
     */
    public function destroyUserSessions(string $userIdentityId): bool
    {
        if ($sessionIds = $this->redis->zrangebyscore($this->keyUser($userIdentityId), '-inf', '+inf')) {
            foreach ($sessionIds ?? [] as $id) {
                parent::destroySession($id);
                $this->redis->del($this->keySession($id));
            }
        }
        $this->redis->del($this->keyUser($userIdentityId));

        return true;
    }

    /**
     * Destroy all expired session's dependencies
     */
    public function removeExpired()
    {
        if ($keys = $this->redis->keys($this->maskUser())) {
            foreach ($keys ?? [] as $key) {
                $this->redis->multi();
                $this->redis->zrangebyscore($key, '-inf', $this->expiredTime());
                $this->redis->zremrangebyscore($key, '-inf', $this->expiredTime());
                $exec = $this->redis->exec();
                if ($sessionIds = $exec[0]) {
                    $this->redis->del(...array_map(function ($sessionId){
                        parent::destroySession($sessionId);
                        return $this->keySession($sessionId);
                    }, $sessionIds));
                }
            }
        }

        return true;
    }

    /**
     * Return all ids of user identity, who have fresh session order by user identity id
     * @return array [userIdentityId => timestamp]
     */
    public function getOnlineUsers(): array
    {
        $userIdentityIds = [];
        if ($keys = $this->redis->keys($this->maskUser())) {
           
            foreach ($keys ?? [] as $key) {
                if ($sessionIds = $this->getSessionIdsByKey($key, true)) {
                    $userIdentityIds[$this->extractUser($key)] = array('last' => $sessionIds[1], 'active' => count($sessionIds)/2);
                }
            }

        }

        return $userIdentityIds;
    }

    /**
     * Get all active sessions by user identity id
     * Sorted DESC by last activity
     * @param int $userIdentityId
     * @return array
     */
    public function getSessionsById(string $userIdentityId): array
    {
        return $this->getSessionsByKey($this->keyUser($userIdentityId));
    }

    /**
     * Get all active sessions
     * @return array
     */
    public function getActiveSessions(): array
    {
        $sessions = [];
        if ($keys = $this->redis->keys($this->maskUser())) {
            sort($keys, SORT_STRING);
            foreach ($keys ?? [] as $key) {
                foreach ($this->getSessionsByKey($key) as $session) {
                    $sessions[] = $session;
                }
            }
        }

        return $sessions;
    }

    /**
     * Get the timestamp of the start of the request with microsecond precision.
     */
    public function time()
    {
        return $_SERVER["REQUEST_TIME_FLOAT"];
    }

    /**
     * Get the timestamp of the expiry of session
     * @param bool $cached save first-time calculated value
     * @return float
     */
    public function expiredTime($cached = true)
    {
        if ($cached) {
            return $this->expiredTimeCache ?: $this->expiredTimeCache = $this->time() - $this->getTimeout();
        }

        return $this->time() - $this->getTimeout();
    }

    protected $expiredTimeCache = null;

    /**
     * Get internal key for store/read a sorted list of sessions
     * @param string $userIdentityId user identity id
     * @return string
     */
    protected function keyUser(string $userIdentityId): string
    {
        return $this->prefixUser() . $userIdentityId;
    }

    /**
     * Extract user from string key
     * @param string $key
     * @return string
     */
    protected function extractUser(string $key): string
    {
        //return preg_replace('|' . $this->prefixUser() . '(\d+)|', '$1', $key) ?? 0;
        return str_replace($this->prefixUser(), "", $key);
    }

    /**
     * Get internal key mask fore read all keys in storage
     * @return string
     */
    protected function maskUser(): string
    {
        return $this->prefixUser() . '*';
    }

    /**
     * Return prefix for create user key
     * @return string
     */
    protected function prefixUser(): string
    {
        return $this->keyPrefix . $this->userKeySeparator;
    }

    /**
     * Get internal key for store/read a list of user identity id
     * @param string $sessionId session id
     * @return string
     */
    protected function keySession(string $sessionId): string
    {
        return $this->prefixSession() . $sessionId;
    }

    /**
     * Return prefix for create session key
     * @return string
     */
    protected function prefixSession(): string
    {
        return $this->keyPrefix . $this->sessionKeySeparator;
    }

    /**
     * Get all active session IDs that could be stored in the redis storage
     * If withScores is true all odd elements represent a timestamp of a previous element
     * @param string $key
     * @param bool $withScores
     * @return array
     */
    private function getSessionIdsByKey(string $key, bool $withScores = false): array
    {
        if ($withScores) {
            return $this->redis->zrevrangebyscore($key, $this->time(), $this->expiredTime(), 'WITHSCORES');
        }

        return $this->redis->zrevrangebyscore($key, $this->time(), $this->expiredTime());
    }

    /**
     * Get all active sessions by internal key.
     * Sorted DESC by last activity
     * @param string $key
     * @return array
     */
    private function getSessionsByKey(string $key): array
    {
        $sessions = [];
        if ($current = $this->getSessionIdsByKey($key)) {
            foreach ($current as $value) {
                $sessions[] = $this->readSession($value);
            }
        }

        return $sessions;
    }
}
