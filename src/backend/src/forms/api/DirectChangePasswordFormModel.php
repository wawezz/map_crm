<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\models\User;
use backend\db\repositories\UserRepositoryInterface;
use backend\events\api\UserPasswordChangedEvent;
use yii\base\Event;

class DirectChangePasswordFormModel extends AbstractFormModel
{
    /**
     * @var string
     */
    public $userId;

    /**
     * @var string
     */
    public $password;

    /**
     * @var \backend\db\repositories\UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    public function rules()
    {
        return [
            [['userId'], 'required', 'message' => 'Incorrect user id.'],
            [
                ['userId'],
                'string',
                'min' => 36,
                'max' => 36,
                'message' => 'Incorrect user id.',
                'tooShort' => 'Incorrect user id.',
                'tooLong' => 'Incorrect user id.',
            ],
            [['userId'], 'validateUser'],
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
        ];
    }

    private $user;

    public function validateUser($attribute)
    {
        $this->user = $this->userRepository->findById($this->userId);

        if (!$this->user) {
            $this->addError($attribute, 'User not found.');
        }
    }

    public function execute(): bool
    {
        $user = $this->user;

        if (null === $user) {
            throw new \RuntimeException('User not found.3');
        }

        $user->password = $this->password;

        $this->userRepository->update($user);

        $event = new UserPasswordChangedEvent;
        $event->user = $user;
        $event->newPassword = $this->password;
        $event->isQuiet = true; // skip send email

        Event::trigger(User::class, UserPasswordChangedEvent::class, $event);

        return true;
    }
}

