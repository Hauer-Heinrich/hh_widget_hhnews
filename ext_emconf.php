<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "hh_widget_hhnews".
 *
 * Auto generated 28-08-2018 11:22
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF['hh_widget_hhnews'] = [
    'title' => 'Hauer-Heinrich - Dashboard News',
    'description' => 'Hauer-Heinrich news widget for ext:dashboard',
    'category' => 'be',
    'author' => 'Christian Hackl',
    'author_email' => 'chackl@hauer-heinrich.de',
    'author_company' => 'Hauer-Heinrich.de',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '1.2.0',
    'autoload' => [
        'psr-4' => [
            'HauerHeinrich\\HhWidgetHhnews\\' => 'Classes'
        ]
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
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
