<?php
// Set you own vendor name.
// Adjust the extension name part of the namespace to your extension key.
namespace TeaminmediasPluswerk\KeSearchHooks;

/**
 * Class AdditionalContentFields
 * @package TeaminmediasPluswerk\KeSearchHooks
 *
 * This class is an example on how to index more fields of the tt_content table without having to write a custom
 * indexer. The default page indexer is used. Two hooks are needed: One adds the field to the list of fields
 * fetched from the tt_content table, the other one adds the field to the content written to the index.
 *
 * See also ext_localconf.php on how to register the hooks.
 */

class AdditionalContentFields {

    public function modifyPageContentFields(&$fields, $pageIndexer)
    {
        // Add the field "subheader" from the tt_content table, which is normally not indexed, to the list of fields.
        $fields .= ",subheader";
    }

    public function modifyContentFromContentElement(string &$bodytext, array $ttContentRow, $pageIndexer)
    {
        // Add the content of the field "subheader" to $bodytext, which is, what will be saved to the index.
        $bodytext .= strip_tags($ttContentRow['subheader']);
    }

    public function contentElementShouldBeIndexed($ttContentRow, $contentElementShouldBeIndexed, $pObj)
    {
        //debug($ttContentRow);
        return $contentElementShouldBeIndexed;
    }
}
