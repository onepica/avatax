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
 * @property OnePica_AvaTax_Model_Service_Avatax_Invoice  _invoiceResource
 * @property OnePica_AvaTax_Model_Service_Avatax_Estimate _estimateResource
 * @property OnePica_AvaTax_Model_Service_Avatax_Ping     _pingResource
 * @property OnePica_AvaTax_Model_Service_Avatax_Address
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax
    extends OnePica_AvaTax_Model_Service_Abstract
{
    /**
     * Service config class name
     */
    const AVATAX_SERVICE_CONFIG_CLASS = 'avatax/service_avatax_config';

    /**
     * OnePica_AvaTax_Model_Service_Avatax constructor.
     *
     * @internal param mixed
     */
    public function __construct()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $this->setStoreId($storeId);
    }

    /**
     * Get estimate resource
     *
     * @return OnePica_AvaTax_Model_Service_Avatax_Estimate
     */
    protected function _getEstimateResource()
    {
        if (null === $this->_estimateResource) {
            $this->_estimateResource = Mage::getModel('avatax/service_avatax_estimate',
                array('service_config' => $this->getServiceConfig()));
        }

        return $this->_estimateResource;
    }

    /**
     * Get invoice resource
     *
     * @return OnePica_AvaTax_Model_Service_Avatax_Invoice
     */
    protected function _getInvoiceResource()
    {
        if (null === $this->_invoiceResource) {
            $this->_invoiceResource = Mage::getModel('avatax/service_avatax_invoice',
                array('service_config' => $this->getServiceConfig()));
        }

        return $this->_invoiceResource;
    }

    /**
     * Get ping resource
     *
     * @return OnePica_AvaTax_Model_Service_Avatax_Ping
     */
    protected function _getPingResource()
    {
        if (null === $this->_pingResource) {
            $this->_pingResource = Mage::getModel('avatax/service_avatax_ping',
                array('service_config' => $this->getServiceConfig()));
        }

        return $this->_pingResource;
    }

    /**
     * Get Address Validator resource
     *
     * @return \OnePica_AvaTax_Model_Service_Avatax_Address
     */
    protected function _getAddressValidatorResource()
    {
        if (null === $this->_addressValidatorResource) {
            return Mage::getModel('avatax/service_avatax_address',
                array('service_config' => $this->getServiceConfig())
            );
        }

        return $this->_addressValidatorResource;
    }

    /**
     * Get rates from Avalara
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return array
     */
    public function getRates($address)
    {
        return $this->_getEstimateResource()->getRates($address);
    }

    /**
     * Get tax detail summary
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return array
     */
    public function getSummary($address)
    {
        return $this->_getEstimateResource()->getSummary($address);
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
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return \OnePica_AvaTax_Model_Service_Avatax_Address
     * @throws \OnePica_AvaTax_Model_Service_Exception_Address
     */
    public function validate($address)
    {
        return $this->_getAddressValidatorResource()->validate($address);
    }

    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer_SalesOrderInvoiceSaveAfter::execute()
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
     * @see OnePica_AvaTax_Model_Observer_SalesOrderCreditmemoSaveAfter::execute()
     * @param Mage_Sales_Model_Order_Creditmemo  $creditmemo
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

    /**
     * Get service config class name
     *
     * @return string
     */
    protected function _getServiceConfigClassName()
    {
        return self::AVATAX_SERVICE_CONFIG_CLASS;
    }
}
