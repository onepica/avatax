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
 * Class OnePica_AvaTax_Model_Service_Avatax
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax
    extends OnePica_AvaTax_Model_Service_Abstract
{
    /**
     * Estimate Resource
     *
     * @var mixed
     */
    protected $_estimateResource;

    /**
     * Invoice Resource
     *
     * @var mixed
     */
    protected $_invoiceResource;

    /**
     * Ping Resource
     *
     * @var mixed
     */
    protected $_pingResource;

    /**
     * Get estimate resource
     *
     * return mixed
     */
    protected function _getEstimateResource()
    {
        if (!$this->_estimateResource) {
            $this->_estimateResource = Mage::getModel('avatax/service_avatax_estimate');
        }
        return $this->_estimateResource;
    }

    /**
     * Get invoice resource
     *
     * return mixed
     */
    protected function _getInvoiceResource()
    {
        if (!$this->_invoiceResource) {
            $this->_invoiceResource = Mage::getModel('avatax/service_avatax_invoice');
        }
        return $this->_invoiceResource;
    }

    /**
     * Get ping resource
     *
     * return mixed
     */
    protected function _getPingResource()
    {
        if (!$this->_pingResource) {
            $this->_pingResource = Mage::getModel('avatax/service_avatax_ping');
        }
        return $this->_pingResource;
    }

    /**
     * Get rates from Avalara
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    public function getRates($item)
    {
        return $this->_getEstimateResource()->getRates($item);
    }

    /**
     * Get tax detail summary
     *
     * @param int|null $addressId
     * @return array
     */
    public function getSummary($addressId = null)
    {
        return $this->_getEstimateResource()->getSummary($addressId);
    }

    /**
     * Test to see if the product carries its own numbers or is calculated based on parent or children
     *
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item|mixed $item
     * @return bool
     */
    public function isProductCalculated($item)
    {
        return $this->_getEstimateResource()->isProductCalculated($item);
    }

    /**
     * Get avatax address validator
     * @return OnePica_AvaTax_Model_Service_Avatax_Address
     */
    public function getAddressValidator()
    {
        return Mage::getSingleton('avatax/service_avatax_address');
    }

    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer::salesOrderPlaceAfter()
     * @param Mage_Sales_Model_Order_Invoice     $invoice
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return bool
     * @throws OnePica_AvaTax_Exception
     * @throws OnePica_AvaTax_Model_Service_Exception_Commitfailure
     * @throws OnePica_AvaTax_Model_Service_Exception_Unbalanced
     */
    public function invoice($invoice, $queue)
    {
        return $this->_getInvoiceResource()->invoice($invoice, $queue);
    }

    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer::salesOrderPlaceAfter()
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return mixed
     * @throws OnePica_AvaTax_Exception
     * @throws OnePica_AvaTax_Model_Service_Exception_Commitfailure
     * @throws OnePica_AvaTax_Model_Service_Exception_Unbalanced
     */
    public function creditmemo($creditmemo, $queue)
    {
        return $this->_getInvoiceResource()->creditmemo($creditmemo, $queue);
    }

    /**
     * Tries to ping AvaTax service with provided credentials
     *
     * @param int $storeId
     * @return bool|array
     */
    public function ping($storeId)
    {
        return $this->_getPingResource()->ping($storeId);
    }
}
