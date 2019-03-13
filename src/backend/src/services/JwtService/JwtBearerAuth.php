<?php


namespace backend\services\JwtService;

use yii\filters\auth\AuthMethod;
use yii\web\Request;

class JwtBearerAuth extends AuthMethod
{
    /**
     * @var JwtService|string|array the [[Jwt]] object or the application component ID of the [[Jwt]].
     */
    public $jwt;
    /**
     * @var string A "realm" attribute MAY be included to indicate the scope
     * of protection in the manner described in HTTP/1.1 [RFC2617].  The "realm"
     * attribute MUST NOT appear more than once.
     */
    public $realm = 'api';
    /**
     * @var string Authorization header schema, default 'Bearer'
     */
    public $schema = 'Bearer';
    /**
     * @var callable a PHP callable that will authenticate the user with the JWT payload information
     * @var callable a PHP callable that will authenticate the user with the JWT payload information
     *
     * ```php
     * function ($token, $authMethod) {
     *    return \backend\models\User::findOne($token->getClaim('id'));
     * }
     * ```
     *
     * If this property is not set, the username information will be considered as an access token
     * while the password information will be ignored. The [[\yii\web\User::loginByAccessToken()]]
     * method will be called to authenticate and login the user.
     */
    public $auth;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->jwt = \Yii::$container->get(JwtService::class);
    }

    /**
     * @param \yii\web\User $user
     * @param \yii\web\Request $request
     * @param \yii\web\Response $response
     * @return mixed|null|\yii\web\IdentityInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function authenticate($user, $request, $response)
    {
        $token = $this->loadToken($request);

        if ($token === null) {
            return null;
        }

        if ($this->auth) {
            $identity = \call_user_func($this->auth, $token, \get_class($this));

            if (null !== $identity) {
                $user->login($identity);
            }
        } else {
            $identity = $user->loginByAccessToken($token, \get_class($this));
        }

        return $identity;
    }

    /**
     * @param \yii\web\Response $response
     */
    public function challenge($response)
    {
        $response->getHeaders()->set(
            'WWW-Authenticate',
            "{$this->schema} realm=\"{$this->realm}\", error=\"invalid_token\", error_description=\"The access token invalid or expired\""
        );
    }

    /**
     * @param \yii\web\Request $request
     * @return \Lcobucci\JWT\Token|null
     * @throws \yii\base\InvalidConfigException
     */
    private function loadToken(Request $request)
    {
        if ($request->getHeaders()->has('Authorization')) {
            $authHeader = $request->getHeaders()->get('Authorization');

            if (!preg_match('/^' . $this->schema . '\s+(.*?)$/', $authHeader, $matches)) {
                return null;
            }

            return $this->jwt->loadToken($matches[1]);
        } elseif ($token = $request->getQueryParam('token')) {
            return $this->jwt->loadToken($token);
        } else {
            throw new \RuntimeException('Token not found.');
        }
    }
}
