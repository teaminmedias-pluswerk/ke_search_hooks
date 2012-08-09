<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Christian Bülter (kennziffer.com) <buelter@kennziffer.com>
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


/***************************************************************
 * This class is an example for a custom indexer for ke_seach,
 * a faceted search extension for TYPO3.
 * Please use it as a kickstarter for your own extensions.
 * It implements a simple indexer for tt_news (although
 * there's already one implemented in ke_search itself).
 ***************************************************************/

class user_kesearchhooks {

	function registerIndexerConfiguration(&$params, $pObj) {

			// add item to "type" field
		$newArray = array(
				'Custom news indexer',
				'customnews',
				t3lib_extMgm::extRelPath('ke_search_hooks') . 'customnews-indexer-icon.gif'
				);
		$params['items'][] = $newArray;

			// enable "sysfolder" field
		$GLOBALS['TCA']['tx_kesearch_indexerconfig']['columns']['sysfolder']['displayCond'] .= ',customnews';
	}

	/**
	 * Custom indexer for ke_search
	 *
	 * @param   array $indexerConfig Configuration from TYPO3 Backend
	 * @param   array $indexerObject Reference to indexer class.
	 * @return  string Output.
	 * @author  Christian Buelter <buelter@kennziffer.com>
	 * @since   Fri Jan 07 2011 16:01:51 GMT+0100
	 */
	public function customIndexer(&$indexerConfig, &$indexerObject) {
		if($indexerConfig['type'] == 'customnews') {
			$content = '';

				// get all the entries to index
				// don't index hidden or deleted elements, BUT
				// get the elements with frontend user group access restrictions
				// or time (start / stop) restrictions.
				// Copy those restrictions to the index.
			$fields = '*';
			$table = 'tt_news';
			$where = 'pid IN (' . $indexerConfig['sysfolder'] . ') AND hidden = 0 AND deleted = 0';
			$groupBy = '';
			$orderBy = '';
			$limit = '';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields,$table,$where,$groupBy,$orderBy,$limit);
			$resCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res);

				// Loop through the records and write them to the index.
			if($resCount) {
				while ( ($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) ) {

						// compile the information which should go into the index
						// the field names depend on the table you want to index!
					$title = strip_tags($record['title']);
					$abstract = strip_tags($record['short']);
					$content = strip_tags($record['description']);
					$fullContent = $title . "\n" . $abstract . "\n" . $content;
					$params = '&tx_ttnews[tt_news]=' . $record['uid'];
					$tags = '#example_tag_1#,#example_tag_2#';
					$additionalFields = array(
							'sortdate' => $record['crdate'],
							'orig_uid' => $record['uid'],
							'orig_pid' => $record['pid'],
							'sortdate' => $record['datetime'],
							);

						// add something to the title, just to identify the entries
						// in the frontend
					$title = '[CUSTOM INDEXER] ' . $title;

						// ... and store the information in the index
					$indexerObject->storeInIndex(
							$indexerConfig['storagepid'],   // storage PID
							$title,                         // record title
							'customnews',                  	// content type
							$indexerConfig['targetpid'],    // target PID: where is the single view?
							$fullContent,                   // indexed content, includes the title (linebreak after title)
							$tags,                          // tags for faceted search
							$params,                        // typolink params for singleview
							$abstract,                      // abstract; shown in result list if not empty
							$record['sys_language_uid'],    // language uid
							$record['starttime'],           // starttime
							$record['endtime'],             // endtime
							$record['fe_group'],            // fe_group
							false,                          // debug only?
							$additionalFields               // additionalFields
							);
				}
				$content = '<p><b>Custom Indexer "' . $indexerConfig['title'] . '": ' . $resCount . ' Elements have been indexed.</b></p>';
			}
			return $content;
		}
	}

		/**
        * Custom Filter Renderer
        *
        * @param   integer $filterUid uid of the filter as created in the backend
        * @param   array $options list of uids
        * @param   tx_kesearch_lib $kesearch_lib caller class
        * @return  string
        * @author  Christian Buelter <buelter@kennziffer.com>
        * @since   Mon Jan 10 2011 14:46:57 GMT+0100
        */
        public function customFilterRenderer($filterUid, $options, tx_kesearch_lib $kesearch_lib) {
                $filterSubpart = '###SUB_FILTER_SELECT###';
                $optionSubpart = '###SUB_FILTER_SELECT_OPTION###';

                        // add standard option "all"
                $optionsContent .= $kesearch_lib->cObj->getSubpart($kesearch_lib->templateCode,$optionSubpart);
				$filters = $kesearch_lib->filters->getFilters();
                $optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent,'###TITLE###', $filters[$filterUid]['title']);
                $optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent,'###VALUE###', '');
                $optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent,'###SELECTED###','');

                        // loop through options
                if (is_array($options)) {
					foreach ($options as $key => $data) {
						$optionsContent .= $kesearch_lib->cObj->getSubpart($kesearch_lib->templateCode, $optionSubpart);
							$optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent,'###ONCLICK###', $kesearch_lib->onclickFilter);
							$optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent,'###TITLE###', $data['title']);
							$optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent,'###VALUE###', $data['value']);
							$optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent,'###SELECTED###', $data['selected'] ? ' selected="selected" ' : '');
							$optionsCount++;
					}
                }

                        // fill markers
                $filterContent = $kesearch_lib->cObj->getSubpart($kesearch_lib->templateCode, $filterSubpart);
                $filterContent = $kesearch_lib->cObj->substituteSubpart ($filterContent, $optionSubpart, $optionsContent, $recursive=1);
                $filterContent = $kesearch_lib->cObj->substituteMarker($filterContent,'###FILTERTITLE###', $filters[$filterUid]['title']);
                $filterContent = $kesearch_lib->cObj->substituteMarker($filterContent,'###FILTERNAME###', 'tx_kesearch_pi1[filter]['.$filterUid.']');
                $filterContent = $kesearch_lib->cObj->substituteMarker($filterContent,'###FILTERID###', 'filter['.$filterUid.']');
                $filterContent = $kesearch_lib->cObj->substituteMarker($filterContent,'###ONCHANGE###', $kesearch_lib->onclickFilter);
                $filterContent = $kesearch_lib->cObj->substituteMarker($filterContent,'###DISABLED###', $optionsCount > 0 ? '' : ' disabled="disabled" ');

                return $filterContent;
        }
}
?>
