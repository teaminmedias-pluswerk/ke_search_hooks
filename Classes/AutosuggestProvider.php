<?php
namespace TeaminmediasPluswerk\KeSearchHooks;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *
 * Class to provide custom values for the ke_search_premium autosuggest feature, in this example we use page titles.
 *
 * Class AutosuggestProvider
 * @package TeaminmediasPluswerk\KeSearchHooks
 */
class AutosuggestProvider {
    public function modifyAutocompleWordList(&$words, $begin, $amount, $pid)
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('pages');
        $queryBuilder
            ->select('title')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->like('title', $queryBuilder->createNamedParameter($queryBuilder->escapeLikeWildcards($begin) . '%'))
            )
            ->setMaxResults(10)
            ->execute()
            ->fetchAll();
        $pages = $queryBuilder->execute()->fetchAll();
        if ($pages) {
            $words = [];
            foreach ($pages as $page) {
                $words[] = $page['title'];
            }
        }
    }
}