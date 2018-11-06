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

// register custom indexer hook
// adjust this to your namespace and class name
// adjust the autoloading information in composer.json, too!
$customIndexerClassName = 'TeaminmediasPluswerk\KeSearchHooks\ExampleIndexer';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['registerIndexerConfiguration'][] = $customIndexerClassName;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['customIndexer'][] = $customIndexerClassName;

// register hooks for indexing additional fields
$additionContentClassName = 'TeaminmediasPluswerk\KeSearchHooks\AdditionalContentFields';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['modifyPageContentFields'][] = $additionContentClassName;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['modifyContentFromContentElement'][] = $additionContentClassName;
