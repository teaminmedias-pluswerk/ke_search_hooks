<?php
/***************************************************************
 *  Copyright notice
 *  (c) 2012-2014 Christian Bülter (kennziffer.com) <buelter@kennziffer.com>
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


/***************************************************************
 * This class is an example for a custom indexer for ke_seach,
 * a faceted search extension for TYPO3.
 * Please use it as a kickstarter for your own extensions.
 * It implements a simple indexer for tt_news (although
 * there's already one implemented in ke_search itself).
 * And it shows how to add more information to your result list,
 * in this case images to ext:tt_news and ext:news results.
 ***************************************************************/
class user_kesearchhooks
{
    var $imageSize = array('width' => 150, 'height' => 150);

    /**
     * add marker to search results
     * display first image from tt_news search results
     * image will be added to the marker ###TEASER###
     * (you may add your own marker to the template and change that)
     * You may set the image width in the typoscript template like this:
     * ke_search_hooks.newsImageWidth = 100
     * @param array $tempMarkerArray
     * @param array $row
     * @param tx_kesearch_lib $pObj
     */
    public function additionalResultMarker(array &$tempMarkerArray, array $row, tx_kesearch_lib $pObj)
    {
        $this->pObj = $pObj;

        // display news image (tt_news)
        if ($row['type'] == 'tt_news') {
            $pathToImages = 'uploads/pics/';
            $lcObj = t3lib_div::makeInstance('tslib_cObj');

            // get the image from the tt_news entry and add it to the teaser
            $fields = 'image';
            $table = 'tt_news';
            $where = 'uid=' . $row['orig_uid'];
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where);
            $resCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
            if ($resCount) {
                $newsRecord = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
                if ($newsRecord['image']) {
                    $images = explode(',', $newsRecord['image']);
                    $imageConf['file'] = $pathToImages . $images[0];
                    $imageConf['file.']['maxW'] = ($GLOBALS['TSFE']->tmpl->setup['ke_search_hooks.']['newsImageWidth'] ? $GLOBALS['TSFE']->tmpl->setup['ke_search_hooks.']['newsImageWidth'] : 50);
                    $imageConf['wrap'] = '<span class="kesearch_newsimage">|</span>';
                    $tempMarkerArray['teaser'] = $lcObj->IMAGE($imageConf) . $tempMarkerArray['teaser'];
                }
            }
        }

        // display news image (ext:news with FAL)
        // get the image from the news entry and add it to the teaser
        if ($row['type'] == 'news') {
            $tempMarkerArray['teaser'] = $this->getImage($row['orig_uid'], 'tx_news_domain_model_news',
                    'fal_media') . $tempMarkerArray['teaser'];
        }


        // display page image (from page properties -> resources -> media using FAL)
        if ($row['type'] == 'page') {
            $tempMarkerArray['teaser'] = $this->getImage($row['orig_uid'], 'pages',
                    'media') . $tempMarkerArray['teaser'];

            // old style (without using FAL):
            /*
            // get media entry
            $page = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
                'media',
                'pages',
                '1=1' .
                ' AND uid=' . $row['orig_uid']
            );

            // process only if media is not empty
            if(!empty($page['media'])) {
                $files = t3lib_div::trimExplode(',', $page['media']);
                $imgConf['file'] = 'uploads/media/' . $files[0];
                $imgConf['file.']['width'] = '80';
                $imgConf['params'] = 'style="float: left; margin-right: 15px"';
                $imageHtml = $this->pObj->cObj->IMAGE($imgConf);
                $tempMarkerArray['teaser'] =  $imageHtml . $tempMarkerArray['teaser'];
            }
             *
             */
        }
    }


    /*
     * get fal media file
     * @param int $newsUid
     * @param string $tablenames
     * @return string $file / empty
     */
    protected function getImage($newsUid, $tablenames, $fieldname)
    {
        $fields = 'identifier,alternative';
        $table = 'sys_file_reference, sys_file';
        $where = 'tablenames = "' . $tablenames . '"';
        $where .= ' AND fieldname = "' . $fieldname . '"';
        $where .= ' AND uid_foreign = ' . intval($newsUid);
        $where .= ' AND uid_local = sys_file.uid';
        $where .= $this->pObj->cObj->enableFields('sys_file_reference');
        $where .= $this->pObj->cObj->enableFields('sys_file');
        $groupBy = '';
        $orderBy = '';
        $limit = 1;
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $where, $groupBy, $orderBy, $limit);

        if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
            return $this->renderImage($row['identifier'], $row['alternative']);
        } else {
            return '';
        }
    }


    /**
     * render the image tag
     * @param string $imagePath
     * @return string
     */
    protected function renderImage($imagePath, $altText = '')
    {
        if ($imagePath != '') {
            $myCObj = new tslib_cObj();

            $imgTSConfig['file'] = 'fileadmin' . $imagePath;
            $imgTSConfig['file.']['maxW'] = $this->imageSize['width'];
            $imgTSConfig['file.']['maxH'] = $this->imageSize['height'];
            $imgTSConfig['altText'] = $altText;

            $imageCode = '<div class="ke_search_image">' . $myCObj->IMAGE($imgTSConfig) . '</div>';

            return $imageCode;
        }
    }




    /**
     * Custom Filter Renderer
     * @param   integer $filterUid uid of the filter as created in the backend
     * @param   array $options list of uids
     * @param   tx_kesearch_lib $kesearch_lib caller class
     * @return  string
     * @author  Christian Buelter <buelter@kennziffer.com>
     * @since   Mon Jan 10 2011 14:46:57 GMT+0100
     */
    public function customFilterRenderer($filterUid, $options, tx_kesearch_lib $kesearch_lib)
    {
        $filterSubpart = '###SUB_FILTER_SELECT###';
        $optionSubpart = '###SUB_FILTER_SELECT_OPTION###';

        // add standard option "all"
        $optionsContent .= $kesearch_lib->cObj->getSubpart($kesearch_lib->templateCode, $optionSubpart);
        $filters = $kesearch_lib->filters->getFilters();
        $optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent, '###TITLE###',
            $filters[$filterUid]['title']);
        $optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent, '###VALUE###', '');
        $optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent, '###SELECTED###', '');

        // loop through options
        if (is_array($options)) {
            foreach ($options as $key => $data) {
                $optionsContent .= $kesearch_lib->cObj->getSubpart($kesearch_lib->templateCode, $optionSubpart);
                $optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent, '###ONCLICK###',
                    $kesearch_lib->onclickFilter);
                $optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent, '###TITLE###',
                    'CUSTOM: ' . $data['title']);
                $optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent, '###VALUE###', $data['value']);
                $optionsContent = $kesearch_lib->cObj->substituteMarker($optionsContent, '###SELECTED###',
                    $data['selected'] ? ' selected="selected" ' : '');
                $optionsCount++;
            }
        }

        // fill markers
        $filterContent = $kesearch_lib->cObj->getSubpart($kesearch_lib->templateCode, $filterSubpart);
        $filterContent = $kesearch_lib->cObj->substituteSubpart($filterContent, $optionSubpart, $optionsContent,
            $recursive = 1);
        $filterContent = $kesearch_lib->cObj->substituteMarker($filterContent, '###FILTERTITLE###',
            $filters[$filterUid]['title']);
        $filterContent = $kesearch_lib->cObj->substituteMarker($filterContent, '###FILTERNAME###',
            'tx_kesearch_pi1[filter][' . $filterUid . ']');
        $filterContent = $kesearch_lib->cObj->substituteMarker($filterContent, '###FILTERID###',
            'filter[' . $filterUid . ']');
        $filterContent = $kesearch_lib->cObj->substituteMarker($filterContent, '###ONCHANGE###',
            $kesearch_lib->onclickFilter);
        $filterContent = $kesearch_lib->cObj->substituteMarker($filterContent, '###DISABLED###',
            $optionsCount > 0 ? '' : ' disabled="disabled" ');

        return $filterContent;
    }
}
