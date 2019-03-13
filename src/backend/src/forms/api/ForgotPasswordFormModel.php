<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\models\User;
use backend\db\repositories\UserRepositoryInterface;
use backend\events\api\UserChangePasswordRequestEvent;
use yii\base\Event;

class ForgotPasswordFormModel extends AbstractFormModel
{

    /**
     * @var string
     */
    public $email;

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
        ];
    }

    public function validateUser($attribute)
    {
        if (!$this->userRepository->findByEmail($this->email)) {
            $this->addError($attribute, 'User not found.');
        }
    }

    public function execute(): bool
    {
        $user = $this->userRepository->findByEmail($this->email);

        if (null === $user) {
            throw new \RuntimeException('User not found.');
        }

        $event = new UserChangePasswordRequestEvent;
        $event->user = $user;

        Event::trigger(User::class, UserChangePasswordRequestEvent::class, $event);

        return true;
    }
}
