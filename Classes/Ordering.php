<?php
namespace TeaminmediasPluswerk\KeSearchHooks;

use TeaminmediasPluswerk\KeSearch\Lib\Db;

/**
 * Class Ordering
 *
 * This is an example how to modify the sorting (ordering) of ke_search results.
 * The hook needs to be registered in ke_search_hooks.
 * Needs at least ke_search 3.7.2.
 *
 * @package TeaminmediasPluswerk\KeSearchHooks
 */
class Ordering
{
    public function getOrdering(& $orderBy, Db $keSearchDb)
    {
        //debug($orderBy);
        //debug($keSearchDb->pObj);
        //$orderBy = 'title, sortdate DESC';
    }

}