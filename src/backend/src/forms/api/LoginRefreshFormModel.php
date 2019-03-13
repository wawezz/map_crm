<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\models\User;
use backend\db\repositories\UserRepositoryInterface;
use backend\events\api\UserLoginEvent;
use backend\services\JwtService\JwtService;
use yii\base\Event;

/**
 * Class LoginRefreshFormModel
 * @package backend\forms\api
 */
class LoginRefreshFormModel extends AbstractFormModel
{
    /**
     * @var string
     */
    public $token;

    /**
     * @var \backend\db\repositories\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var \backend\services\JwtService\JwtService
     */
    private $jwtService;

    public function __construct(
        JwtService $jwtService,
        UserRepositoryInterface $userRepository
    ) {
        parent::__construct();

        $this->jwtService = $jwtService;
        $this->userRepository = $userRepository;
    }

    public function rules()
    {
        return [
            [['token'], 'required', 'message' => 'Incorrect token.'],
            [['token'], 'validateToken'],
        ];
    }

    public function validateToken($attribute)
    {
        $token = $this->jwtService->loadToken($this->token);

        if ($token->getClaim('type') !== 'refresh') {
            $this->addError($attribute, 'Incorrect token.');

            return;
        }

        $user = $this->userRepository->findById($token->getClaim('uid'));

        if (null === $user) {
            $this->addError($attribute, 'User not found.');
        }

    }

    public function execute(): array
    {
        $result = [
            'user' => null,
            'tokens' => null,
        ];

        $token = $this->jwtService->loadToken($this->token);

        $user = $this->userRepository->findById($token->getClaim('uid'));

        if (!$user) {
            throw new \RuntimeException('User not found.');
        }


        $result['user'] = $user->publicBundle();

        $result['tokens'] = [
            'access' => (string)$this->jwtService->createAccessToken($user->id),
            'refresh' => (string)$this->jwtService->createRefreshToken($user->id),
        ];

        $event = new UserLoginEvent;
        $event->user = $user;

        Event::trigger(User::class, UserLoginEvent::class, $event);

        return $result;
    }
}
