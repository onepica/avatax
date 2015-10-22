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
 * Class OnePica_AvaTax_Model_Validator
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */

class OnePica_AvaTax_Model_Validator extends Mage_Core_Model_Factory
{
    /**
     * Service
     * @var OnePica_AvaTax_Model_Service_Abstract
     */
    protected $_service;

    /**
     * Class constructor
     * @param array $params
     * @throws OnePica_AvaTax_Exception
     */
    public function __construct($params = array())
    {
        $activeService = $this->_getConfigHelper()->getAvataxActiveService();
        $this->_service = Mage::getSingleton('avatax/service')->factory($activeService, $params);
    }

    /**
     * Get Service
     *
     * return OnePica_AvaTax_Model_Service_Abstract
     */
    protected function _getService()
    {
        return $this->_service;
    }

    /**
     * Get service validator
     *
     * @param Mage_Customer_Model_Address_Abstract $address
     * @return mixed
     */
    public function getServiceValidator(Mage_Customer_Model_Address_Abstract $address)
    {
        return $this->_getService()->getAddressValidator($address);
    }

    /**
     * Get config helper
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }
}
