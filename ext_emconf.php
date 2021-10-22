<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Reserve',
    'description' => 'This extension allows you to reserve tickets for one or more persons using a period table and a form. Each reservation has it’s own QR Code that can be scanned at the event.',
    'category' => 'plugin',
    'author' => 'Markus Kugler, Pascal Rinker',
    'author_email' => 'projects@jweiland.net',
    'author_company' => 'jweiland.net',
    'state' => 'stable',
    'version' => '1.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'events2' => ''
        ],
    ],
];
