<?php
/**
 * Claudiucreanga_Import extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Claudiucreanga
 * @package   Claudiucreanga_Import
 * @copyright 2016 Claudiucreanga
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Claudiu Creanga
 */
declare(strict_types=1);

namespace Claudiucreanga\Import\Cron;

/**
 * Class FieldsHeader
 * @package Claudiucreanga\Import\Cron
 */
class FieldsHeader
{
    public function getFieldsHeader()
    {
        return array(
            "setName", //string
            "setSubimport", //string
            "setAuthorName", //string
            "setPrice", //int
            "setSku", //string
            "setRelated", //string, comma separated skus
            "setUpsell", //string, comma separated skus
            "setCrosssell", //string, comma separated skus
            "setQty", //int
            "setIsInStock", //bool
            "setAlsoAvailableEbook", //bool
            "setAuthorAbout", //string
            "setAvailableFormats", //string
            "setAwardName", //string
            "setBicbasicUk", //string
            "setBinding", // string
            "setBisac", // string
            "setBookStatus", //string
            "setCatalogId", // string
            "setCategoryIds", // string, list of integers comma separated
            "setChapterExtract", //string
            "setContentsList", //string
            "setContributorLabel", //string
            "setDescription", //string
            "setDistributedPublisher", //string
            "setEbookUrl", //string
            "setEditionType", //string
            "setFeaturedContributor", //string
            "setIllustrated", //string
            "setImage",
            "setImageLabel",
            "setMetaDescription", //string
            "setMetaKeyword", //string
            "setMetaImport", //string
            "setNumberOfIllustrations", //string
            "setNumberOfPages", //string
            "setPublicationDate", //string
            "setPublisher", //string
            "setReviewAuthor1", //string
            "setReviewAuthor2", //string
            "setReviewAuthor3", //string
            "setReviewAuthor4", //string
            "setReviewDescription1", //string
            "setReviewDescription2", //string
            "setReviewDescription3", //string
            "setReviewDescription4", //string
            "setReviewName1", //string
            "setReviewName2", //string
            "setReviewName3", //string
            "setReviewName4", //string
            "setReviewImport1",
            "setReviewImport2",
            "setReviewImport3",
            "setReviewImport4",
            "setSamplesImport",  //string
            "setShortDescription", //string
            "setSize", //string
            "setSmallImage",
            "setSmallImageLabel",
            "setStandfirst",  //string
            "setStatus", // bool
            "setTaxClassId", // int (0 - none, 1 - default, 2 - taxable, 4 - shipping), for now it appears that they are sending it as a string "Taxable Goods"
            "setThumbnail",
            "setThumbnailLabel",
            "setUrlKey", //string
            "setUrlPath",  //string, rarely used
            "setVisibility", //int 1 to 4
            "setWeight" //float
        );
    }
}

