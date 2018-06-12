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

        $service = $this->_getServiceCertificate();
        /** @var \Varien_Data_Collection $customerCertificates */
        $customerCertificates = $service->getAllCustomerCertificates($this->getCustomerCode());

        // calculate totals
        $this->_totalRecords = count($customerCertificates);

        $this->_setIsLoaded();

        // paginate and add items
        $from = ($this->getCurPage() - 1) * $this->getPageSize();
        $to = $from + $this->getPageSize() - 1;
        $isPaginated = $this->getPageSize() > 0;

        $cnt = 0;
        /** @var \Varien_Object $customerCertificate */
        foreach ($customerCertificates as $customerCertificate) {
            $cnt++;
            if ($isPaginated && ($cnt < $from || $cnt > $to)) {
                continue;
            }

            $this->addItem($customerCertificate);
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
}
