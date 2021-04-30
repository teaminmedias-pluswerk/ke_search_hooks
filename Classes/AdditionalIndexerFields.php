<?php
namespace TeaminmediasPluswerk\KeSearchHooks;

/**
 * Class AdditionalIndexerFields
 * @package TeaminmediasPluswerk\KeSearchHooks
 */
class AdditionalIndexerFields {
    public function registerAdditionalFields(&$additionalFields) {
        $additionalFields[] = 'mysorting';
    }
}