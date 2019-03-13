<?php

namespace backend\services\JwtService;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Claim\Factory as ClaimFactory;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Parsing\Decoder;
use Lcobucci\JWT\Parsing\Encoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use yii\base\InvalidConfigException;

class JwtService
{
    /**
     * @var array Supported algorithms
     * @todo Add RSA, ECDSA suppport
     */
    public $supportedAlgs = [
        'HS256' => 'Lcobucci\JWT\Signer\Hmac\Sha256',
        'HS384' => 'Lcobucci\JWT\Signer\Hmac\Sha384',
        'HS512' => 'Lcobucci\JWT\Signer\Hmac\Sha512',
    ];

    /**
     * @var string|array|null $key The key, or map of keys.
     * @todo Add RSA, ECDSA key file support
     */
    public $key;

    public function __construct()
    {
        $this->key = hash('sha256', getenv('MYSQL_USER_PASSWORD'));
    }

    /**
     * @param string $uid
     * @return \Lcobucci\JWT\Token
     * @throws \BadMethodCallException
     */
    public function createAccessToken(string $uid, int $time = 3600): Token
    {
        return $this->getBuilder()
            ->setIssuer(BASE_URL)
            ->setAudience(BASE_URL)
            // ->setId($user->id, true)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration(time() + $time)
            ->set('type', 'access')
            ->set('uid', $uid)
            ->sign(new Sha256, $this->key)
            ->getToken();
    }

    /**
     * @param string $uid
     * @return \Lcobucci\JWT\Token
     * @throws \BadMethodCallException
     */
    public function createRefreshToken(string $uid)
    {
        return $this->getBuilder()
            ->setIssuer(BASE_URL)
            ->setAudience(BASE_URL)
            // ->setId($user->id, true)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration(time() + (86400 * 7))
            ->set('type', 'refresh')
            ->set('uid', $uid)
            ->sign(new Sha256, $this->key)
            ->getToken();
    }

    /**
     * @see [[Lcobucci\JWT\Builder::__construct()]]
     * @param \Lcobucci\JWT\Parsing\Encoder|null $encoder
     * @param \Lcobucci\JWT\Claim\Factory|null $claimFactory
     * @return Builder
     */
    public function getBuilder(Encoder $encoder = null, ClaimFactory $claimFactory = null)
    {
        return new Builder($encoder, $claimFactory);
    }

    /**
     * @see [[Lcobucci\JWT\Parser::__construct()]]
     * @param \Lcobucci\JWT\Parsing\Decoder|null $decoder
     * @param \Lcobucci\JWT\Claim\Factory|null $claimFactory
     * @return Parser
     */
    public function getParser(Decoder $decoder = null, ClaimFactory $claimFactory = null)
    {
        return new Parser($decoder, $claimFactory);
    }

    /**
     * @see [[Lcobucci\JWT\ValidationData::__construct()]]
     * @param null $currentTime
     * @return ValidationData
     */
    public function getValidationData($currentTime = null)
    {
        $data = new ValidationData($currentTime);
        $data->setIssuer(BASE_URL);
        $data->setAudience(BASE_URL);

        return $data;
    }

    /**
     * Parses the JWT and returns a token class
     * @param string $token JWT
     * @param bool $validate
     * @param bool $verify
     * @return Token|null
     * @throws \yii\base\InvalidConfigException
     */
    public function loadToken($token, $validate = true, $verify = true)
    {
        try {
            $token = $this->getParser()->parse((string)$token);
        } catch (\RuntimeException $e) {
            \Yii::warning('Invalid JWT provided: ' . $e->getMessage(), 'jwt');

            return null;
        } catch (\InvalidArgumentException $e) {
            \Yii::warning('Invalid JWT provided: ' . $e->getMessage(), 'jwt');

            return null;
        }

        if ($validate && !$this->validateToken($token)) {
            return null;
        }

        if ($verify && !$this->verifyToken($token)) {
            return null;
        }

        return $token;
    }

    /**
     * Validate token
     * @param Token $token token object
     * @param null $currentTime
     * @return bool
     */
    public function validateToken(Token $token, $currentTime = null)
    {
        $data = $this->getValidationData($currentTime);

        // @todo Add claims for validation
        return $token->validate($data);
    }

    /**
     * Validate token
     * @param Token $token token object
     * @return bool
     * @throws \BadMethodCallException
     * @throws \yii\base\InvalidConfigException
     */
    public function verifyToken(Token $token)
    {
        // $alg = $token->getHeader('alg');
        $alg = 'HS256';

        if (empty($this->supportedAlgs[$alg])) {
            throw new InvalidConfigException('Algorithm not supported');
        }

        /** @var \Lcobucci\JWT\Signer $signer */
        $signer = \Yii::createObject($this->supportedAlgs[$alg]);

        return $token->verify($signer, $this->key);
    }
}
