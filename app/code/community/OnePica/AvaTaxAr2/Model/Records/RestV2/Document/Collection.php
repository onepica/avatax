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
    protected $_customerCode = null;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

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
        $this->_customerCode = $customerCode;

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

        try {
            /** @var \Varien_Data_Collection $customerCertificates */
            $customerCertificates = $this->_getServiceCertificate()->getAllCustomerCertificates($this->getCustomerCode());
            $this->_items = $customerCertificates->getItems();
            $this->_totalRecords = count($customerCertificates->getItems());
            $this->_setIsLoaded();
            $this->_renderFilters();
            $this->_renderOrders();
            $this->_renderLimit();
        } catch (\Exception $ex) {
            Mage::logException($ex);
            $this->_getCoreSession()->addError($ex->getMessage());
        }

        return $this;
    }

    /**
     * Render conditions
     *
     * @return  Varien_Data_Collection
     */
    protected function _renderFilters()
    {
        if (!$this->_filters) {
            return $this;
        }

        foreach ($this->_filters as $filter) {
            $this->_items = array_filter(
                $this->_items,
                function ($item) use ($filter) {
                    $valueItem = $item->getData($filter->getField());
                    $valueFilter = mb_strtolower($filter->getValue());
                    if (is_bool($valueItem)) {
                        return $valueItem == $valueFilter;
                    }

                    if (is_object($valueItem)) {
                        return mb_strpos(mb_strtolower($valueItem->name), $valueFilter) !== false ? true : false;
                    }

                    return mb_strpos(mb_strtolower($valueItem), $valueFilter) !== false ? true : false;
                }
            );
        }

        $this->_totalRecords = count($this->_items);

        return $this;
    }

    /**
     * Render limit
     *
     * @return  Varien_Data_Collection
     * @throws \Exception
     */
    protected function _renderLimit()
    {
        $from = ($this->getCurPage() - 1) * $this->getPageSize();
        $to = $from + $this->getPageSize();
        $isPaginated = $this->getPageSize() > 0;

        if ($isPaginated) {
            $this->_items = array_slice($this->_items, $from, $to);
        }

        return $this;
    }

    /**
     * Render orders
     *
     * @return  Varien_Data_Collection
     */
    protected function _renderOrders()
    {
        foreach ($this->_orders as $fieldId => $dir) {
            usort(
                $this->_items,
                function ($a, $b) use ($fieldId) {
                    $paramA = $a->getData($fieldId);
                    $paramB = $b->getData($fieldId);
                    if (is_numeric($paramA) && is_numeric($paramB)) {
                        return $paramA - $paramB;
                    }
                    if (is_object($paramA) && is_object($paramB)) {
                        return strcmp($paramA->name, $paramB->name);
                    }

                    return strcmp($paramA, $paramB);
                }
            );

            if (mb_strtoupper($dir) === self::SORT_ORDER_DESC) {
                $this->_items = array_reverse($this->_items);
            }
        }

        return $this;
    }

    /**
     * Just to change return type
     *
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
     *
     * @inheritdoc
     *
     * @return OnePica_AvaTaxAr2_Model_Records_RestV2_Document[]
     */
    public function getItems()
    {
        return parent::getItems();
    }

    /**
     * @return null|string
     */
    public function getCustomerCode()
    {
        return $this->_customerCode;
    }

    /**
     * @return OnePica_AvaTaxAr2_Model_Service_Avatax_Certificate
     */
    protected function _getServiceCertificate()
    {
        return Mage::getSingleton('avataxar2/service_avatax_certificate');
    }

    /**
     * Get Core Session
     *
     * @return \Mage_Core_Model_Session
     */
    protected function _getCoreSession()
    {
        return Mage::getSingleton('core/session');
    }
}
