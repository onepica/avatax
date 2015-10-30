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
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * The AvaTax16 Invoice model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax16_Invoice extends OnePica_AvaTax_Model_Service_Avatax16_Tax
{
    /**
     * An array of line numbers to product ids
     *
     * @var array
     */
    protected $_lineToItemId = array();

    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer::salesOrderPlaceAfter()
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return bool
     * @throws OnePica_AvaTax_Exception
     * @throws OnePica_AvaTax_Model_Service_Exception_Commitfailure
     * @throws OnePica_AvaTax_Model_Service_Exception_Unbalanced
     */
    public function invoice($invoice, $queue)
    {
        $order = $invoice->getOrder();
        $storeId = $order->getStoreId();
        $invoiceDate = $this->_convertGmtDate($invoice->getCreatedAt(), $storeId);

        $shippingAddress = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();
        if (!$shippingAddress) {
            throw new OnePica_AvaTax_Exception($this->__('There is no address attached to this order'));
        }

        $configModel = $this->getService()->getServiceConfig()->init($storeId);
        $config = $configModel->getLibConfig();

        // Set up document for request
        $this->_request = new OnePica_AvaTax16_Document_Request();

        // set up header
        $header = new OnePica_AvaTax16_Document_Request_Header();
        $header->setAccountId($config->getAccountId());
        $header->setCompanyCode($config->getCompanyCode());
        $header->setTransactionType(self::TRANSACTION_TYPE_SALE);
        $header->setDocumentCode($invoice->getIncrementId());
        $header->setCustomerCode($this->_getConfigHelper()->getSalesPersonCode($storeId));
        $header->setVendorCode(self::DEFAULT_VENDOR_CODE);
        $header->setTransactionDate($invoiceDate);
        $header->setTaxCalculationDate($this->_getDateModel()->date('Y-m-d'));
        $header->setDefaultLocations($this->_getHeaderDefaultLocations($shippingAddress));
        $header->setDefaultAvalaraGoodsAndServicesType($this->_getConfigHelper()
            ->getDefaultAvalaraGoodsAndServicesType($storeId));
        $header->setDefaultAvalaraGoodsAndServicesModifierType($this->_getConfigHelper()
            ->getDefaultAvalaraGoodsAndServicesModifierType($storeId));
        $header->setDefaultTaxPayerCode($this->_getConfigHelper()->getDefaultTaxPayerCode($storeId));
        $header->setDefaultUseType($this->_getConfigHelper()->getDefaultUseType($storeId));
        $header->setDefaultBuyerType($this->_getConfigHelper()->getDefaultBuyerType($storeId));

        $this->_request->setHeader($header);
    }

    /**
     * Retrieve converted date taking into account the current time zone and store.
     *
     * @param string $gmt
     * @param int    $storeId
     * @return string
     */
    protected function _convertGmtDate($gmt, $storeId)
    {
        return Mage::app()->getLocale()->storeDate($storeId, $gmt)->toString(Varien_Date::DATE_INTERNAL_FORMAT);
    }
}
