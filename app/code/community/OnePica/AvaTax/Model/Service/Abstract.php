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
 * abstract class OnePica_AvaTax_Model_Service_Abstract
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Model_Service_Abstract extends Varien_Object
{
    /**
     * Get rates from Service
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    abstract public function getRates($item);

    /**
     * Get tax detail summary
     *
     * @param int|null $addressId
     * @return array
     */
    abstract public function getSummary($addressId);

    /**
     * Test to see if the product carries its own numbers or is calculated based on parent or children
     *
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item|mixed $item
     * @return bool
     */
    abstract public function isProductCalculated($item);

    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer::salesOrderPlaceAfter()
     * @param Mage_Sales_Model_Order_Invoice     $invoice
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return bool
     */
    abstract public function invoice($invoice, $queue);

    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer::salesOrderPlaceAfter()
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return bool
     */
    abstract public function creditmemo($creditmemo, $queue);

    /**
     * Tries to ping AvaTax service with provided credentials
     *
     * @param int $storeId
     * @return bool|array
     */
    abstract public function ping($storeId);

    /**
     * Get service address validator
     * @return mixed
     */
    abstract public function getAddressValidator();

    /**
     * Service config.
     * @var null|OnePica_AvaTax_Model_Service_Abstract_Config
     */
    private $_config = null;

    /**
     * Get service config.
     * @return null|OnePica_AvaTax_Model_Service_Abstract_Config
     */
    public function getServiceConfig()
    {
        return $this->_config;
    }

    /**
     * Set service config.
     * @param null|OnePica_AvaTax_Model_Service_Abstract_Config $config
     */
    public function setServiceConfig($config)
    {
        $this->_config = $config;
    }

    /**
     * Get config helper
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }
}
