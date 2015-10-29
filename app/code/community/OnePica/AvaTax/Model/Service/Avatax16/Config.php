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
 * @class OnePica_AvaTax_Model_Service_Avatax16_Config
 * The AvaTax Config Model, which registers config settings with the AvaTax SDK
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax16_Config extends OnePica_AvaTax_Model_Service_Abstract_Config
{
    /**
     * The AvaTax config helper.
     *
     * @var OnePica_AvaTax_Helper_Config
     */
    protected $_getConfigHelper = null;

    /**
     * Set AvaTax16 lib Config
     * @param OnePica_AvaTax16_Config $config
     */
    public function setLibConfig($config)
    {
        $this->_config = $config;
    }

    /**
     * Get Avatax16 lib config
     * @return OnePica_AvaTax16_Config
     */
    public function getLibConfig()
    {
        return $this->_config;
    }

    /**
     * Initializes the AvaTax16 SDK with connection settings found in the Admin config.
     *
     * @param int $storeId
     * @return OnePica_AvaTax_Model_Config
     */
    public function init($storeId)
    {
        if (is_null($this->_config)) {
            $this->setLibConfig(Mage::getModel('OnePica_AvaTax16_Config'));
            $this->getLibConfig()->setBaseUrl($this->_getConfigHelper()->getServiceUrl($storeId));
            $this->getLibConfig()->setAccountId($this->_getConfigHelper()->getServiceAccountId($storeId));
            $this->getLibConfig()->setCompanyCode($this->_getConfigHelper()->getCompanyCode($storeId));
            $this->getLibConfig()->setAuthorizationHeader($this->_getConfigHelper()->getServiceKey($storeId));
        }

        return $this;
    }

    /**
     * Get resource connection
     * @return null|OnePica_AvaTax16_TaxService
     */
    public function getTaxConnection()
    {
        if (is_null($this->_connection)){
            $this->init(Mage::app()->getStore());
            $this->_connection = new OnePica_AvaTax16_TaxService($this->getLibConfig());
        }
        return $this->_connection;
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    public function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }
}