<?php
/**
 * OnePica_AvaTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Document resource collection
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Records_RestV2_Document_Collection extends Varien_Data_Collection
{
    /**
     * Construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setItemObjectClass('OnePica_AvaTaxAr2_Model_Records_RestV2_Document');
    }

    /**
     * Set filter for collection by the customer.
     *
     * @param int $customerCode
     * @return $this
     */
    public function addCustomerFilter($customerCode)
    {
        // TODO: implement

        return $this;
    }

    /**
     * Lauch data collecting
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return \OnePica_AvaTaxAr2_Model_Records_RestV2_Document_Collection
     * @throws \Exception
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        // calculate totals
        $this->_totalRecords = count($this->_getItemsArray());
        $this->_setIsLoaded();

        // paginate and add items
        $from = ($this->getCurPage() - 1) * $this->getPageSize();
        $to = $from + $this->getPageSize() - 1;
        $isPaginated = $this->getPageSize() > 0;

        $cnt = 0;
        foreach ($this->_getItemsArray() as $itemData) {
            $cnt++;
            if ($isPaginated && ($cnt < $from || $cnt > $to)) {
                continue;
            }

            $object = $this->getNewEmptyItem();
            $object->setData($itemData);

            $this->addItem($object);
        }

        return $this;
    }

    /**
     * Just to change return type
     * @inheritdoc
     *
     * @return OnePica_AvaTaxAr2_Model_Records_RestV2_Document
     */
    public function getNewEmptyItem()
    {
        return parent::getNewEmptyItem();
    }

    /**
     * Just to change return type
     * @inheritdoc
     *
     * @return OnePica_AvaTaxAr2_Model_Records_RestV2_Document[]
     */
    public function getItems()
    {
        return parent::getItems();
    }

    /**
     * Fake items
     *
     * @TODO: remove
     * @return array
     */
    protected function _getItemsArray()
    {
        return array(
            array(
                "id"                  => 50,
                "companyId"           => 662794,
                "signedDate"          => "2018-03-29",
                "expirationDate"      => "2019-03-29",
                "filename"            => "n/a",
                "valid"               => true,
                "verified"            => false,
                "exemptPercentage"    => 0,
                "isSingleCertificate" => false,
                "exemptionReason"     => array(
                    "id"   => 61,
                    "name" => "FEDERAL GOV"
                ),
                "createdDate"         => "2018-03-29T13:41:16",
                "modifiedDate"        => "2018-04-12",
                "pageCount"           => 0,
                "exposureZone"        => array(
                    "id"          => 140,
                    "name"        => "Alaska",
                    "tag"         => "EZ_US_AK",
                    "description" => "Alaska Sales Tax",
                    "region"      => "AK",
                    "country"     => "US"
                )
            ),
            array(
                "id"                  => 51,
                "companyId"           => 662794,
                "signedDate"          => "2016-02-01",
                "expirationDate"      => "2020-12-31",
                "filename"            => "39535_51_1522332263.pdf",
                "valid"               => true,
                "verified"            => false,
                "exemptPercentage"    => 0,
                "isSingleCertificate" => false,
                "exemptionReason"     => array(
                    "id"   => 55,
                    "name" => "AGRICULTURE"
                ),
                "createdDate"         => "2018-03-29T14:04:22",
                "modifiedDate"        => "2018-04-12",
                "pageCount"           => 0,
                "exposureZone"        => array(
                    "id"          => 100,
                    "name"        => "Pennsylvania",
                    "tag"         => "EZ_US_PA",
                    "description" => "Pennsylvania Sales Tax",
                    "region"      => "PA",
                    "country"     => "US"
                )
            ),
            array(
                "id"                  => 56,
                "companyId"           => 662794,
                "signedDate"          => "2016-02-01",
                "expirationDate"      => "2020-12-31",
                "filename"            => "39535_56_1523547372.pdf",
                "valid"               => true,
                "verified"            => false,
                "exemptPercentage"    => 0,
                "isSingleCertificate" => false,
                "exemptionReason"     => array(
                    "id"   => 70,
                    "name" => "RESALE"
                ),
                "createdDate"         => "2018-04-12T15:36:10",
                "modifiedDate"        => "2018-04-12",
                "pageCount"           => 0,
                "exposureZone"        => array(
                    "id"          => 100,
                    "name"        => "Pennsylvania",
                    "tag"         => "EZ_US_PA",
                    "description" => "Pennsylvania Sales Tax",
                    "region"      => "PA",
                    "country"     => "US"
                )
            ),
        );
    }
}
