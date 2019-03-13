<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\common\generator\UuidGenerator;
use backend\db\models\User;
use backend\db\repositories\UserRepositoryInterface;
use backend\events\api\UserUpdatedEvent;
use backend\db\models\File;
use backend\db\repositories\FileRepositoryInterface;
use backend\services\JwtService\JwtService;
use yii\base\Event;

/**
 * Class UserUpdateFormModel
 * @package backend\forms\api
 */
class UserUpdateFormModel extends AbstractFormModel
{

    /**
     * @var string
     */
    public $id;

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
    public $avatarName;

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
     * @var string
     */
    public $updatedBy = 0;

    /**
     * @var \backend\services\JwtService\JwtService
     */
    private $jwtService;

    /**
     * @var \backend\db\repositories\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var \backend\db\repositories\FileRepositoryInterface
     */
    private $fileRepository;

    public function __construct(UserRepositoryInterface $userRepository, FileRepositoryInterface $fileRepository, jwtService $jwtService)
    {
        parent::__construct();
        $this->jwtService = $jwtService;
        $this->userRepository = $userRepository;
        $this->fileRepository = $fileRepository;
    }

    public function rules()
    {
        return [
            [['id'], 'required', 'message' => 'Incorrect ID.'],
            [['id'], 'validateUser'],
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
            [
                ['password'],
                'string',
                'min' => 6,
                'max' => 128,
                'message' => 'Incorrect password.',
                'tooShort' => 'Incorrect password.',
                'tooLong' => 'Incorrect password.',
            ],
            [
                ['name'],
                'string',
                'min' => 3,
                'max' => 128,
                'message' => 'Incorrect name.',
                'tooShort' => 'Incorrect name.',
                'tooLong' => 'Incorrect name.',
            ],
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
            [['updatedBy'], 'validateAdmin'],
            [['email', 'name', 'sipId', 'sipPass'], 'trim'],
        ];
    }

    private $user;
    private $admin;

    public function validateAdmin($attribute)
    {
        if($this->updatedBy){
            $this->admin = $this->userRepository->findByID($this->updatedBy);
            if (!$this->admin) {
                $this->addError($attribute, 'Admin not found.');
            }
        }else{
            $this->admin = \Yii::$app->user->identity;
        }
    }

    public function validateUser($attribute)
    {

        $this->user = $this->userRepository->findByID($this->id);
        if (!$this->user) {
            $this->addError($attribute, 'User not found.');
        }
    }

    public function validateEmail($attribute)
    {

        if ($this->email && $this->user->email !== $this->email) {
            $checkUser = $this->userRepository->findByEmail($this->email);

            if ($this->checkUser) {
                $this->addError($attribute, 'Mail already registred.');
            }
        }
    }

    public function execute(): array
    {


        $admin = $this->admin;
        $user = $this->user;
        $changes = 0;


        if ($admin->roleId != 1 && ($user->roleId != $this->roleId || $user->groupId != $this->groupId)) {
            throw new \ErrorException('Only admin can change some data.', 418);
        }

        if ($this->password) {
            $user->password = $this->password;
            $changes+= 1;
        }

        if ($this->email && $user->email != $this->email) {
            $user->email = $this->email;
            $changes+= 1;
        }

        if ($this->name && $user->name != $this->name) {
            $user->name = $this->name;
            $changes+= 1;
        }

        if ($this->roleId && $user->roleId != $this->roleId) {
            $user->roleId = $this->roleId;
            $changes+= 1;
        }

        if ($this->groupId && $user->groupId != $this->groupId) {
            $user->groupId = $this->groupId;
            $changes+= 1;
        }

        $user->groupId = ($this->roleId == 1)?0:$user->groupId;

        if ($this->avatarId > 0) {
            $this->fileRepository->removeById($user->avatarId, $user->avatarPath.$user->avatarName);

            $user->avatarId = $this->avatarId;
            $changes+= 1;
        }
 
        if($this->avatarName) $user->avatarName = $this->avatarName;

        if ($user->sipId != $this->sipId) {
            $user->sipId = $this->sipId;
            $changes+= 1;
        }

        if ($user->sipPass != $this->sipPass) {
            $user->sipPass = $this->sipPass;
            $changes+= 1;
        }

        if($changes == 0){
            throw new \ErrorException('No changes detected.', 418);
        }

        if (!$this->userRepository->update($user)) {
            throw new \ErrorException('Failed to update user.');
        }

        $result['user'] = $user->publicBundle();
  
        $result['tokens'] = [
            'access' => (string) $this->jwtService->createAccessToken($user->id),
            'refresh' => (string) $this->jwtService->createRefreshToken($user->id),
        ];

        $event = new UserUpdatedEvent;
        $event->user = $user;
        if ($this->password) {
            $event->newPassword = $this->password;
        }

        $event->isQuiet = false; // send email

        Event::trigger(User::class, UserUpdatedEvent::class, $event);

        return $result;
    }
}
