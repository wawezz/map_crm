<?php

namespace backend\common\log;

use backend\exceptions\FormValidationException;
use yii\helpers\ArrayHelper;

class SentryTarget extends \notamedia\sentry\SentryTarget
{
    /**
     * @inheritdoc
     */
    public function export()
    {
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp, $traces) = $message;

            $data = [
                'level' => static::getLevelName($level),
                'timestamp' => $timestamp,
                'tags' => ['category' => $category],
            ];

            if ($text instanceof \Throwable || $text instanceof \Exception) {
                if ($text instanceof FormValidationException) {
                    $data['extra'] = [
                        'errors' => $text->getErrors(),
                    ];
                }

                $this->client->captureException($text, $data);

                return;
            } elseif (is_array($text)) {
                if (isset($text['msg'])) {
                    $data['message'] = $text['msg'];
                    unset($text['msg']);
                }

                if (isset($text['tags'])) {
                    $data['tags'] = ArrayHelper::merge($data['tags'], $text['tags']);
                    unset($text['tags']);
                }

                $data['extra'] = $text;
            } else {
                $data['message'] = $text;
            }

            if ($this->context) {
                $data['extra']['context'] = parent::getContextMessage();
            }

            if (\is_callable($this->extraCallback) && isset($data['extra'])) {
                $data['extra'] = \call_user_func($this->extraCallback, $text, $data['extra']);
            }

            $this->client->capture($data, $traces);
        }
    }
}
