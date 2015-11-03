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
     * Estimate Resource
     *
     * @var mixed
     */
    protected $_estimateResource;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $args = func_get_args();
        if (empty($args[0])) {
            $args[0] = array();
        }

        parent::__construct($args[0]);
        $this->setServiceConfig(Mage::getSingleton('avatax/service_avatax16_config')->init(Mage::app()->getStore()));
    }

    /**
     * Get estimate resource
     *
     * return mixed
     */
    protected function _getEstimateResource()
    {
        return Mage::getSingleton('avatax/service_avatax16_estimate', array('service' => $this));
    }

    /**
     * Get invoice resource
     *
     * return mixed
     */
    protected function _getInvoiceResource()
    {
        return Mage::getSingleton('avatax/service_avatax16_invoice', array('service' => $this));
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
     * @param Mage_Sales_Model_Order_Invoice $invoice
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
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
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
        return Mage::getSingleton('avatax/service_avatax16_ping', array('service' => $this))->ping($storeId);
    }

    /**
     * Get service address validator
     *
     * @return OnePica_AvaTax_Model_Service_Abstract_Tools
     */
    public function getAddressValidator()
    {
        return Mage::getSingleton('avatax/service_avatax16_address', array('service' => $this));
    }
}
