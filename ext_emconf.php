<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Reserve',
    'description' => 'This extension allows you to reserve tickets for one or more persons using a period table and a form. Each reservation has it’s own QR Code that can be scanned at the event.',
    'category' => 'plugin',
    'author' => 'Stefan Froemken, Hoja Mustaffa Abdul Latheef',
    'author_email' => 'projects@jweiland.net',
    'author_company' => 'jweiland.net',
    'state' => 'stable',
    'version' => '3.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.8-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'events2' => '',
        ],
    ],
];
