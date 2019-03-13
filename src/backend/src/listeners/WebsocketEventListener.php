<?php

namespace backend\listeners;

use backend\db\models\User;
use backend\events\api\UserLoginEvent;
use backend\db\models\Task;
use backend\events\api\TaskAddEvent;
use backend\services\WebsocketService\WebsocketService;
use yii\helpers\VarDumper;

class WebsocketEventListener implements EventListenerInterface
{
    /**
     * @var \backend\services\WebsocketService\WebsocketService
     */
    private $websocketService;

    public function __construct(
        WebsocketService $websocketService
    ) {
        $this->websocketService = $websocketService;
    }

    public function rules(): array
    {
        return [
            [User::class, UserLoginEvent::class, [$this, 'sendNotify']],
            [Task::class, TaskAddEvent::class, [$this, 'sendTaskAddNotify']],
        ];
    }

    public function sendNotify(UserLoginEvent $event)
    {
        $user = $event->user;

        if (!$user) {
            \Yii::warning('The socket event was not emitted because the member not set.');

            return;
        }

        $this->websocketService->pub($user->getFullId(), [
            'event' => \get_class($event),
            'user' => $user->publicBundle(),
            'logined' => 123
        ]);

        \Yii::info('The socket event emitted.');
    }

    public function sendTaskAddNotify(TaskAddEvent $event)
    {
        $user = $event->user;
        $task = $event->task;

        if (!$user) {
            \Yii::warning('The socket event was not emitted because the member not set.');

            return;
        }

        $this->websocketService->pub($user->getFullId(), [
            'event' => \get_class($event),
            'user' => $user->publicBundle(),
            'task' => $task->publicBundle()
        ]);

        \Yii::info('The socket event emitted.');
    }
}
