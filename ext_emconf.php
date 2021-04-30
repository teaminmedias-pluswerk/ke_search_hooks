<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Faceted Search Hooks Example',
    'description' => 'Hooks example for ke_search. Feel free to use this as a kickstarter for your own custom indexer or hooks. Implements a news indexer as example.',
    'category' => 'backend',
    'version' => '3.3.0',
    'dependencies' => 'ke_search',
    'state' => 'stable',
    'author' => 'Christian Buelter',
    'author_email' => 'christian.buelter@web.de',
    'author_company' => 'TPWD GmbH',
    'constraints' => array(
        'depends' => array(
            'typo3' => '9.5.0-10.4.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
