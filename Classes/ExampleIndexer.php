<?php
// Set you own vendor name.
// Adjust the extension name part of the namespace to your extension key.
namespace TeaminmediasPluswerk\KeSearchHooks;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

// Set you own class name.
class ExampleIndexer
{
    // Set a key for your indexer configuration.
    // Add this key to the $GLOBALS[...] array in Configuration/TCA/Overrides/tx_kesearch_indexerconfig.php, too!
    protected $indexerConfigurationKey = 'customindexer';

    /**
     * Adds the custom indexer to the TCA of indexer configurations, so that
     * it's selectable in the backend as an indexer type, when you create a
     * new indexer configuration.
     *
     * @param array $params
     * @param type $pObj
     */
    public function registerIndexerConfiguration(&$params, $pObj)
    {
        // Set a name and an icon for your indexer.
        $customIndexer = array(
            '[CUSTOM] News-Indexer (ext:news)',
            $this->indexerConfigurationKey,
            ExtensionManagementUtility::extPath('ke_search_hooks') . 'customnews-indexer-icon.gif'
        );
        $params['items'][] = $customIndexer;
    }


    /**
     * Custom indexer for ke_search.
     *
     * @param   array $indexerConfig Configuration from TYPO3 Backend.
     * @param   array $indexerObject Reference to indexer class.
     * @return  string Message containing indexed elements.
     * @author  Christian Buelter <christian.buelter@pluswerk.ag>
     */
    public function customIndexer(&$indexerConfig, &$indexerObject)
    {
        if ($indexerConfig['type'] == $this->indexerConfigurationKey) {
            $content = '';

            // Get all the entries to index.
            // Don't index hidden or deleted elements, but get the elements 
            // with frontend user group access restrictions or time (start / stop)
            // restrictions in order to copy those restrictions to the index.
            //
            // Since TYPO3 v8, database access is managed via the Doctrine DBAL layer using the Connection Pool class.
            // The old TYPO3 Database wrapper TYPO3_DB is deprecated since TYPO3 v8 and removed since TYPO3 v9.
            // See: https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/Database/Migration/Index.html
            //
            // A standard connection automatically adds "restrtictions" to handle hidden, deleted, 
            // time (start / stop), etc. fields of a record.
            // See: https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/Database/RestrictionBuilder/Index.html#database-restriction-builder
            // To adhere to the principle metioned above, we first remove all restrictions and then add those again, 
            // we want to keep: the "DeletedRestriction" and "HiddenRestriction".
            
            $fields = ['*']; // Array of table fields.
            $table = 'tx_news_domain_model_news';
            
            // Doctrine DBAL using Connection Pool.
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);
            $queryBuilder = $connection->createQueryBuilder();
            
            // Handle restrictions.
            $queryBuilder
                ->getRestrictions
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
                ->add(GeneralUtility::makeInstance(HiddenRestriction::class));

            $statement = $queryBuilder
                ->select(...$fields)
                ->from($table)
                ->where(
                  $queryBuilder->expr()->in(
                    'pid',
                    $queryBuilder->createNamedParameter($indexerConfig['sysfolder'], \PDO::PARAM_INT)
                  )
                )
                ->execute();

            // Loop through the records and write them to the index.
            $counter = 0;
                
            while ($record = $statement->fetch()) {
                // Compile the information, which should go into the index.
                // The field names depend on the table you want to index!
                $title    = strip_tags($record['title']);
                $abstract = strip_tags($record['teaser']);
                $content  = strip_tags($record['bodytext']);

                $fullContent = $title . "\n" . $abstract . "\n" . $content;

                // Link to detail view
                $params = '&tx_news_pi1[news]=' . $record['uid']
                    . '&tx_news_pi1[controller]=News&tx_news_pi1[action]=detail';

                // Tags
                // If you use Sphinx, use "_" instead of "#" (configurable in the extension manager).
                $tags = '#example_tag_1#,#example_tag_2#';

                // Additional information
                $additionalFields = array(
                    'sortdate' => $record['crdate'],
                    'orig_uid' => $record['uid'],
                    'orig_pid' => $record['pid'],
                    'sortdate' => $record['datetime'],
                );

                // Add something to the title, just to identify the entries
                // in the frontend.
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


            return $content;
        }
        
    }

}
