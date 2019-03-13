<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\common\generator\UuidGenerator;
use backend\db\models\User;
use backend\db\repositories\UserRepositoryInterface;
use backend\events\api\UserCreatedEvent;
use backend\services\JwtService\JwtService;
use yii\base\Event;

/**
 * Class RegistrationFormModel
 * @package backend\forms\api
 */
class RegistrationFormModel extends AbstractFormModel
{

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $roleId;

    /**
     * @var int
     */
    public $groupId;

    /**
     * @var int
     */
    public $avatarId;

    /**
     * @var string
     */
    public $password;

    /**
     * @var int
     */
    public $sipId;

    /**
     * @var string
     */
    public $sipPass;

    /**
     * @var \backend\db\repositories\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var \backend\services\JwtService\JwtService
     */
    private $jwtService;

    /**
     * @var \backend\db\common\generator\UuidGenerator
     */
    private $uuidGenerator;

    public function __construct(
        JwtService $jwtService,
        UserRepositoryInterface $userRepository,
        UuidGenerator $uuidGenerator
    ) {
        parent::__construct();

        $this->jwtService = $jwtService;
        $this->userRepository = $userRepository;
        $this->uuidGenerator = $uuidGenerator;

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
            [['email'], 'email', 'checkDNS' => true, 'message' => 'Incorrect email.'],
            [['email'], 'validateUser'],
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
            [['name'], 'required', 'message' => 'Incorrect name.'],
            [
                ['name'],
                'string',
                'min' => 3,
                'max' => 128,
                'message' => 'Incorrect name.',
                'tooShort' => 'Incorrect name.',
                'tooLong' => 'Incorrect name.',
            ],
            [['roleId'], 'required', 'message' => 'Incorrect role id.'],
            [
                ['roleId'],
                'integer',
                'min' => 1,
                'message' => 'Incorrect role id.',
                'tooSmall' => 'Incorrect role id.',
            ],
            [
                ['groupId'],
                'integer',
                'min' => 0,
                'message' => 'Incorrect group id.',
                'tooSmall' => 'Incorrect group id.',
            ],
            [
                ['avatarId'],
                'integer',
                'min' => 0,
                'message' => 'Incorrect avatar id.',
                'tooSmall' => 'Incorrect avatar id.',
            ],
            [['email', 'name', 'sipId', 'sipPass'], 'trim'],
        ];
    }

    public function validateUser($attribute)
    {
        if ($this->userRepository->findByEmail($this->email)) {
            $this->addError($attribute, 'User already registered.');
        }
    }

    public function execute(): array
    {

        $result = [
            'user' => null,
            'tokens' => null,
        ];

        $nextId = $this->uuidGenerator->generate();

        $user = new User;
        $user->id = $nextId;
        $user->email = $this->email;
        $user->name = $this->name;
        $user->roleId = $this->roleId;
        $user->groupId = ($this->roleId == 1)?0:$this->groupId;
        $user->avatarId = $this->avatarId;
        $user->password = $this->password;
        $user->sipId = $this->sipId;
        $user->sipPass = $this->sipPass;

        if (!$this->userRepository->insert($user)) {
            throw new \ErrorException('Failed to insert user.');
        }

        $user = $this->userRepository->findById($nextId);

        if (!$user) {
            throw new \ErrorException('Failed to create user.');
        }

        $result['user'] = $user->publicBundle();

        $result['tokens'] = [
            'access' => (string) $this->jwtService->createAccessToken($user->id),
            'refresh' => (string) $this->jwtService->createRefreshToken($user->id),
        ];

        try {
            $event = new UserCreatedEvent;
            $event->user = $user;
            $event->password = $this->password;

            Event::trigger(User::class, UserCreatedEvent::class, $event);
        } catch (\Exception $e) {
            $this->userRepository->delete($user);

            throw $e;
        }

        return $result;
    }
}
