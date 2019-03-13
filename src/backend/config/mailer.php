<?php

use backend\common\mailer\Mailer;

return [
    'class' => Mailer::class,
    'useFileTransport' => false,
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => getenv('SMTP_HOST'),
        'username' => getenv('SMTP_USER'),
        'password' => getenv('SMTP_PASSWORD'),
        'port' => getenv('SMTP_PORT'),
        'encryption' => getenv('SMTP_ENCRYPT'),
        // 'encryption' => 'ssl',
        'timeout' => 5,
    ],
    'messageConfig' => [
        'from' => [getenv('SMTP_FROM') => 'Map crm Team'],
        'replyTo' => 'team@map-crm.lc',
    ],
];
