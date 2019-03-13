<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\models\User;
use backend\db\repositories\UserRepositoryInterface;
use backend\events\api\UserLoginEvent;
use backend\services\JwtService\JwtService; 
use yii\base\Event;

/**
 * Class LoginFormModel
 * @package backend\forms\api
 */
class LoginFormModel extends AbstractFormModel
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

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
            [['email'], 'required', 'message' => 'Incorrect email.'],
            [
                ['email'],
                'string',
                'min' => 6,
                'max' => 128,
                'message' => 'Incorrect email.',
                'tooShort' => 'Incorrect email.',
                'tooLong' => 'Incorrect email.',
            ],
            [['email'], 'email', 'message' => 'Incorrect email.'],
            [['password'], 'required', 'message' => 'Incorrect password.'],
            [
                ['password'],
                'string',
                'min' => 6,
                'max' => 128,
                'message' => 'Incorrect password.',
                'tooShort' => 'Incorrect password.',
                'tooLong' => 'Incorrect password.',
            ],
            [['email'], 'validateUser'],
        ];
    }

    public function validateUser($attribute)
    {
        $user = $this->userRepository->findByEmail($this->email);

        if (null === $user) {
            $this->addError($attribute, 'Invalid email or password.');
        } elseif (null === $user->passwordHash) {
            $user->password = $this->password;

            $this->userRepository->update($user);
        } else {
            $result = \Yii::$app->security->validatePassword($this->password, $user->passwordHash);
            
            if (!$result) {
                $this->addError($attribute, 'Invalid email or password.');
            }
        }
    }

    public function execute(): array
    {
        $result = [
            'user' => null,
            'tokens' => null,
        ];

        $user = $this->userRepository->findByEmail($this->email);

        if (!$user) {
            throw new \RuntimeException('User not found.');
        }

        $time = 3600;

        if($user->roleId == 3) $time = 157784760; //5 years

        $result['user'] = $user->publicBundle();

        $result['tokens'] = [
            'access' => (string)$this->jwtService->createAccessToken($user->id, $time),
            'refresh' => (string)$this->jwtService->createRefreshToken($user->id),
        ];

        $event = new UserLoginEvent;
        $event->user = $user;

        Event::trigger(User::class, UserLoginEvent::class, $event);

        return $result;
    }
}
