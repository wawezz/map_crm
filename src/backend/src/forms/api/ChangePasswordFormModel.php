<?php

namespace backend\forms\api;

use backend\common\model\AbstractFormModel;
use backend\db\models\User;
use backend\db\repositories\UserRepositoryInterface;
use backend\events\api\UserPasswordChangedEvent;
use yii\base\Event;

class ChangePasswordFormModel extends AbstractFormModel
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
     * @var string
     */
    public $secure;

    /**
     * @var integer
     */
    public $time;

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
            [['secure'], 'required', 'message' => 'Incorrect secure phrase.'],
            [['secure'], 'string', 'message' => 'Incorrect secure phrase.'],
            [['time'], 'required', 'message' => 'Incorrect secure time.'],
            [['time'], 'validateTime'],
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
        ];
    }

    public function validateTime($attribute)
    {
        $now = time();
        $time = (int)$this->time;

        if ($time < 1524800000 || $time > $now) {
            $this->addError($attribute, 'Incorrect secure time.');
        } elseif (($now - $time) > 86400) {
            $this->addError($attribute, 'Secure phrase is expired.');
        }
    }

    private $user;

    public function validateUser($attribute)
    {
        $this->user = $this->userRepository->findByEmail($this->email);

        if (!$this->user) {
            $this->addError($attribute, 'User not found.');
        } elseif ($this->user->getSecurePhrase($this->time) !== $this->secure) {
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
        $event->isQuiet = false; // send email

        Event::trigger(User::class, UserPasswordChangedEvent::class, $event);

        return true;
    }
}
