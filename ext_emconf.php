<?php
$EM_CONF['hh_widget_hhnews'] = [
    'title' => 'Hauer-Heinrich - Dashboard News',
    'description' => 'Hauer-Heinrich news widget for ext:dashboard',
    'category' => 'be',
    'author' => 'Christian Hackl',
    'author_email' => 'chackl@hauer-heinrich.de',
    'author_company' => 'Hauer-Heinrich.de',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '1.0.0',
    'autoload' => [
        'psr-4' => [
            'HauerHeinrich\\HhWidgetHhnews\\' => 'Classes'
        ]
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
            'php' => '7.2.0-7.4.99',
            'dashboard' => ''
        ],
        'conflicts' => [],
        'suggests' => []
    ],
    'autoload' => [
        'psr-4' => [
            'HauerHeinrich\\HhWidgetHhnews\\' => 'Classes',
        ],
    ],
];
