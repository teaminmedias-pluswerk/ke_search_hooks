<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Christian B�lter (kennziffer.com) <buelter@kennziffer.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

	// add new custom filter renderer
$GLOBALS['TCA']['tx_kesearch_filters']['columns']['rendertype']['config']['items'][] = array (
	'Custom filter',
	'customfilter'
);

$GLOBALS['TCA']['tx_kesearch_filters']['types']['customfilter'] = array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1;;1-1-1, title;;;;2-2-2, rendertype;;;;3-3-3, wrap;;;;4-4-4, options');

?>
