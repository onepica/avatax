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
 * The AvaTax Config Model, which registers config settings with the AvaTax SDK
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Service_AvaTax_Config extends Varien_Object
{
    /**
     * @var \Avalara\AvaTaxRestV2\AvaTaxClient
     */
    protected $_client = null;

    /**
     * @var null
     */
    protected $_store = null;

    /**
     * @var OnePica_AvaTaxAr2_Model_Service_Avatax_Log_Interpreter|null
     */
    protected $_logInterpreter = null;

    /**
     * OnePica_AvaTaxAr2_Model_Service_AvaTax_Config constructor.
     */
    public function __construct()
    {
        $this->_logInterpreter = Mage::getModel('avataxar2/service_avatax_log_interpreter');
    }

    /**
     * @param  null|bool|int|Mage_Core_Model_Store $store
     *
     * @return \Avalara\AvaTaxRestV2\AvaTaxClient|null
     */
    public function getClient($store = null)
    {
        if (null === $this->_client) {
            $this->_store = $store;
            $this->_client = new Avalara\AvaTaxRestV2\AvaTaxClient(
                $this->_getHelper()->getAppName(),
                $this->_getHelper()->getAppVersion(),
                $this->_getHelper()->getMachineName(),
                $this->_getConfigHelper()->getServiceUrl()
            );

            $this->_client->withLicenseKey(
                $this->_getConfigHelper()->getServiceAccountId($store),
                $this->_getConfigHelper()->getServiceKey($store)
            );

            $this->_client->_logsCallback = function ($params = array()) {
                $this->getLogInterpreter()->interpret($this->_client, $this->_store);
            };
            Closure::bind($this->_client->_logsCallback, $this);
        }

        return $this->_client;
    }

    /**
     * @return OnePica_AvaTaxAr2_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avataxar2');
    }

    /**
     * @return OnePica_AvaTaxAr2_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avataxar2/config');
    }

    /**
     * @return OnePica_AvaTaxAr2_Model_Service_Avatax_Log_Interpreter
     */
    protected function getLogInterpreter()
    {
        return $this->_logInterpreter;
    }
}
