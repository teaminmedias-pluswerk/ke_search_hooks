<?php
/***************************************************************
 *  Copyright notice
 *  (c) 2012 Christian Bülter (kennziffer.com) <buelter@kennziffer.com>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

if (!defined("TYPO3_MODE")) {
    die("Access denied.");
}

// Register custom indexer.
// Adjust this to your namespace and class name.
// Adjust the autoloading information in composer.json, too!
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['registerIndexerConfiguration'][] =
    \TeaminmediasPluswerk\KeSearchHooks\ExampleIndexer::class;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['customIndexer'][] =
    \TeaminmediasPluswerk\KeSearchHooks\ExampleIndexer::class;

// Register hooks for indexing additional fields.
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['modifyPageContentFields'][] =
    \TeaminmediasPluswerk\KeSearchHooks\AdditionalContentFields::class;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['modifyContentFromContentElement'][] =
    \TeaminmediasPluswerk\KeSearchHooks\AdditionalContentFields::class;

// Register hook to check if a content element should be indexed
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['contentElementShouldBeIndexed'][] =
    \TeaminmediasPluswerk\KeSearchHooks\AdditionalContentFields::class;

// Register hook to add a custom autosuggest provider (ke_search_premium feature)
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search_premium']['modifyAutocompleWordList'][] =
    \TeaminmediasPluswerk\KeSearchHooks\AutosuggestProvider::class;

// Register hook to
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['additionalResultMarker'][] =
    \TeaminmediasPluswerk\KeSearchHooks\AdditionalResulMarker::class;