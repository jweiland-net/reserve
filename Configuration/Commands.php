<?php
return [
    'reserve:remove_inactive_orders' => [
        'class' => \JWeiland\Reserve\Command\RemoveInactiveOrdersCommand::class
    ],
    'reserve:send_mails' => [
        'class' => \JWeiland\Reserve\Command\SendMailsCommand::class
    ],
    'reserve:remove_past_periods' => [
        'class' => \JWeiland\Reserve\Command\RemovePastPeriodsCommand::class
    ]
];
