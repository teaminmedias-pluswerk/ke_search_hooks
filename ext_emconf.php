<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Faceted Search Hooks Example',
    'description' => 'Hooks example for ke_search. Feel free to use this as a kickstarter for your own custom indexer or hooks. Implements a news indexer as example.',
    'category' => 'backend',
    'version' => '2.0.0',
    'dependencies' => 'ke_search',
    'state' => 'stable',
    'author' => 'Christian Buelter',
    'author_email' => 'christian.buelter@inmedias.de',
    'author_company' => 'team.inmedias',
    'constraints' => array(
        'depends' => array(
            'typo3' => '8.7.0-8.7.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
