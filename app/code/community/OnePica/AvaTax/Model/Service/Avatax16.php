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
 * @class OnePica_AvaTax_Model_Service_Avatax16
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax16
    extends OnePica_AvaTax_Model_Service_Abstract
{
    /**
     * Estimate Resources
     *
     * @var mixed[]
     */
    protected $_estimateResources;

    /**
     * Invoice Resources
     *
     * @var mixed[]
     */
    protected $_invoiceResources;

    /**
     * Ping Resources
     *
     * @var mixed[]
     */
    protected $_pingResources;

    /**
     * Address Validation Resources
     *
     * @var mixed[]
     */
    protected $_addressValidationResources;

    /**
     * OnePica_AvaTax_Model_Service_Avatax16 constructor.
     *
     * @param mixed
     */
    public function __construct()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $this->reInitConfig($storeId);
    }

    /**
     * Re-init ServiceConfig
     *
     * @param int $storeId
     * @return $this
     */
    public function reInitConfig($storeId)
    {
        $this->setCurrentStoreId($storeId);
        if (!$this->getServiceConfig()) {
            $this->setServiceConfig(Mage::getModel('avatax/service_avatax16_config')->init($this->getCurrentStoreId()));
        }
        return $this;
    }

    /**
     * Get estimate resource
     *
     * return mixed
     */
    protected function _getEstimateResource()
    {
        if (!isset($this->_estimateResources[$this->getCurrentStoreId()])) {
            $this->_estimateResources[$this->getCurrentStoreId()] = Mage::getModel('avatax/service_avatax16_estimate',
                array('service_config' => $this->getServiceConfig()));
        }
        $resource = $this->_estimateResources[$this->getCurrentStoreId()];
        return $resource;
    }

    /**
     * Get invoice resource
     *
     * return mixed
     */
    protected function _getInvoiceResource()
    {
        if (!isset($this->_invoiceResources[$this->getCurrentStoreId()])) {
            $this->_invoiceResources[$this->getCurrentStoreId()] = Mage::getModel('avatax/service_avatax16_invoice',
                array('service_config' => $this->getServiceConfig()));
        }
        $resource = $this->_invoiceResources[$this->getCurrentStoreId()];
        return $resource;
    }

    /**
     * Get ping resource
     *
     * return mixed
     */
    protected function _getPingResource()
    {
        if (!isset($this->_pingResources[$this->getCurrentStoreId()])) {
            $this->_pingResources[$this->getCurrentStoreId()] = Mage::getModel('avatax/service_avatax16_ping',
                array('service_config' => $this->getServiceConfig()));
        }
        $resource = $this->_pingResources[$this->getCurrentStoreId()];
        return $resource;
    }

    /**
     * Get Address Validator resource
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address $address
     * @return OnePica_AvaTax_Model_Service_Avatax16_Address
     */
    protected function _getAddressValidatorResource($address)
    {
        if (!isset($this->_addressValidationResources[$this->getCurrentStoreId()])) {
            $this->_addressValidationResources[$this->getCurrentStoreId()] =
                Mage::getModel('avatax/service_avatax16_address',
                    array('service_config' => $this->getServiceConfig(), 'address' => $address)
                );
        }
        $resource = $this->_addressValidationResources[$this->_currentStoreId];
        return $resource;
    }

    /**
     * Get rates from Service
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
    public function getSummary($addressId)
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
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer::salesOrderPlaceAfter()
     * @param Mage_Sales_Model_Order_Invoice     $invoice
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return bool
     */
    public function invoice($invoice, $queue)
    {
        return $this->_getInvoiceResource()->invoice($invoice, $queue);
    }

    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer::salesOrderPlaceAfter()
     * @param Mage_Sales_Model_Order_Creditmemo  $creditmemo
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return bool
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
     * Get service address validator
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address $address
     * @return \OnePica_AvaTax_Model_Service_Avatax16_Address
     */
    public function getAddressValidator($address)
    {
        return $this->_getAddressValidatorResource($address);
    }
}
