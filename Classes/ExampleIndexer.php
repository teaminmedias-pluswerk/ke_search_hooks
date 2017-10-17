<?php
namespace TeaminmediasPluswerk\KeSearchHooks;

use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class ExampleIndexer
{
    protected $indexerConfigurationKey = 'customnews';

    public function debugTest()
    {
        DebugUtility::debug('test');
    }

    /**
     * Adds the custom indexer to the TCA of indexer configurations, so that
     * it's selectable in the backend as an indexer type when you create a
     * new indexer configuration.
     *
     * @param array $params
     * @param type $pObj
     */
    public function registerIndexerConfiguration(&$params, $pObj)
    {

        // add item to "type" field
        $customIndexer = array(
            'Custom news indexer',
            $this->indexerConfigurationKey,
            ExtensionManagementUtility::extRelPath('ke_search_hooks') . 'customnews-indexer-icon.gif'
        );
        $params['items'][] = $customIndexer;

        // enable "sysfolder" field
        $GLOBALS['TCA']['tx_kesearch_indexerconfig']['columns']['sysfolder']['displayCond']
            .= ',' . $this->indexerConfigurationKey;
    }


    /**
     * Custom indexer for ke_search
     *
     * @param   array $indexerConfig Configuration from TYPO3 Backend
     * @param   array $indexerObject Reference to indexer class.
     * @return  string Message containing indexed elements
     * @author  Christian Buelter <christian.buelter@pluswerk.ag>
     */
    public function customIndexer(&$indexerConfig, &$indexerObject)
    {
        if ($indexerConfig['type'] == $this->indexerConfigurationKey) {
            $content = '';

            // get all the entries to index
            // don't index hidden or deleted elements, but
            // get the elements with frontend user group access restrictions
            // or time (start / stop) restrictions, in order to copy those restrictions to the index.
            $fields = '*';
            $table = 'tt_news';
            $where = 'pid IN (' . $indexerConfig['sysfolder'] . ') AND hidden = 0 AND deleted = 0';
            $groupBy = '';
            $orderBy = '';
            $limit = '';
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where, $groupBy, $orderBy, $limit);
            $resCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res);

            // Loop through the records and write them to the index.
            if ($resCount) {
                $counter = 0;
                while (($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
                    // compile the information which should go into the index
                    // the field names depend on the table you want to index!
                    $title      = strip_tags($record['title']);
                    $abstract   = strip_tags($record['short']);
                    $content    = strip_tags($record['description']);

                    $fullContent = $title . "\n" . $abstract . "\n" . $content;

                    // Link to the single page
                    $params = '&tx_ttnews[tt_news]=' . $record['uid'];

                    // Tags
                    // If you youse Sphinx, use "_" instead of "#" (configurable in the extension manager)
                    $tags = '#example_tag_1#,#example_tag_2#';

                    // Additional information
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
                        $this->indexerConfigurationKey, // content type
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
                    $counter++;
                }

                $content =
                    '<p><b>Custom Indexer "'
                    . $indexerConfig['title'] . '": ' . $counter
                    . ' Elements have been indexed.</b></p>';
            }

            return $content;
        }
    }

}