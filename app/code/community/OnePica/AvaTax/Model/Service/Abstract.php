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
 * Abstract class OnePica_AvaTax_Model_Service_Abstract
 *
 * @class OnePica_AvaTax_Model_Service_Abstract
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Model_Service_Abstract extends Varien_Object
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
     * Address validator resource
     *
     * @var mixed
     */
    protected $_addressValidatorResource;

    /**
     * Set Store Id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setCurrentStoreId($storeId);
        if (!$this->getServiceConfig()) {
            $this->setServiceConfig(
                Mage::getModel($this->_getServiceConfigClassName())->init($this->getCurrentStoreId())
            );
        }

        // update service config for each resource
        if (null !== $this->_estimateResource) {
            $this->_estimateResource->setServiceConfig($this->getServiceConfig());
        }

        if (null !== $this->_invoiceResource) {
            $this->_invoiceResource->setServiceConfig($this->getServiceConfig());
        }

        if (null !== $this->_pingResource) {
            $this->_pingResource->setServiceConfig($this->getServiceConfig());
        }

        if (null !== $this->_addressValidatorResource) {
            $this->_addressValidatorResource->setServiceConfig($this->getServiceConfig());
        }

        return $this;
    }

    /**
     * Get rates from Service
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return array
     */
    abstract public function getRates($address);

    /**
     * Get tax detail summary
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return array
     */
    abstract public function getSummary($address);

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
     * @see OnePica_AvaTax_Model_Observer_SalesOrderInvoiceSaveAfter::execute()
     * @param Mage_Sales_Model_Order_Invoice     $invoice
     * @param OnePica_AvaTax_Model_Records_Queue $queue
     * @return bool
     */
    abstract public function invoice($invoice, $queue);

    /**
     * Save order in AvaTax system
     *
     * @see OnePica_AvaTax_Model_Observer_SalesOrderCreditmemoSaveAfter::execute()
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
     *
     * @param mixed $address
     * @return mixed
     */
    abstract public function validate($address);

    /**
     * Get service config class name
     *
     * @return string
     */
    abstract protected function _getServiceConfigClassName();

    /**
     * Service configs for each store
     *
     * @var array|OnePica_AvaTax_Model_Service_Abstract_Config[]
     */
    protected $_configs = array();

    /**
     * Current store Id
     *
     * @var null|int
     */
    protected $_currentStoreId;

    /**
     * Get Current Store Id
     *
     * @return null|int
     */
    public function getCurrentStoreId()
    {
        return $this->_currentStoreId;
    }

    /**
     * Set Current Store Id
     *
     * @param int $storeId
     */
    public function setCurrentStoreId($storeId)
    {
        $this->_currentStoreId = $storeId;
    }

    /**
     * Get service config.
     *
     * @return null|OnePica_AvaTax_Model_Service_Abstract_Config
     */
    public function getServiceConfig()
    {
        $serviceConfig = isset($this->_configs[$this->getCurrentStoreId()])
                       ? $this->_configs[$this->getCurrentStoreId()]
                       : null;
        return $serviceConfig;
    }

    /**
     * Set service config.
     *
     * @param null|OnePica_AvaTax_Model_Service_Abstract_Config $config
     */
    public function setServiceConfig($config)
    {
        $this->_configs[$this->getCurrentStoreId()] = $config;
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
