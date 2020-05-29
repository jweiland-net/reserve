<?php
return [
    'reserve:archive_orders' => [
        'class' => \JWeiland\Reserve\Command\ArchiveOrdersFromPastPeriodsCommand::class
    ],
    'reserve:remove_inactive_orders' => [
        'class' => \JWeiland\Reserve\Command\RemoveInactiveOrdersCommand::class
    ],
];
