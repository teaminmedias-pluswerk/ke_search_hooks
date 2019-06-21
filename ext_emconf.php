<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Faceted Search Hooks Example',
    'description' => 'Hooks example for ke_search. Feel free to use this as a kickstarter for your own custom indexer or hooks. Implements a news indexer as example.',
    'category' => 'backend',
    'version' => '3.0.0',
    'dependencies' => 'ke_search',
    'state' => 'stable',
    'author' => 'Christian Buelter',
    'author_email' => 'christian.buelter@inmedias.de',
    'author_company' => 'team.inmedias',
    'constraints' => array(
        'depends' => array(
            'typo3' => '9.5.0-9.5.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
