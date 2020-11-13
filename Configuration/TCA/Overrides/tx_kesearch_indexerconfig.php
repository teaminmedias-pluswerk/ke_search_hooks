<?php
// Add you own indexer to the array, use a comma to join more indexers. 
$GLOBALS['TCA']['tx_kesearch_indexerconfig']['columns']['sysfolder']['displayCond'] .= ',' . \TeaminmediasPluswerk\KeSearchHooks\ExampleIndexer::KEY;
