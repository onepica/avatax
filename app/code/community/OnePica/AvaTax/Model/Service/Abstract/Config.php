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
 * @class OnePica_AvaTax_Model_Service_Abstract_Tools
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Model_Service_Abstract_Config extends Varien_Object
{
    /**
     * Config key
     */
    const CONFIG_KEY = 'Magento';

    /**
     * App name
     */
    const APP_NAME = 'OP_AvaTax by One Pica';

    /**
     * Calculate action
     */
    const ACTION_CALC = 1;

    /**
     * Calculate, submit action
     */
    const ACTION_CALC_SUBMIT = 2;

    /**
     * Calculate, submit, commit action
     */
    const ACTION_CALC_SUBMIT_COMMIT = 3;

    /**
     * Region filter disable mode
     */
    const REGIONFILTER_OFF = 0;

    /**
     * Region filter tax mode
     */
    const REGIONFILTER_TAX = 1;

    /**
     * Number of times a queue item will try to send
     */
    const QUEUE_ATTEMPT_MAX = 5;

    /**
     * Disable action
     */
    const ACTION_DISABLE = 0;

    /**
     * Region filter all mode
     */
    const REGIONFILTER_ALL = 2;

    /**
     * Resource  connection
     * @var
     */
    protected $_connection = null;

    /**
     * The lib config object.
     *
     * @var
     */
    protected $_config = null;

    /**
     * Get resource  connection
     * @return mixed
     */
    abstract public function getTaxConnection();

    /**
     * Init config
     * @param $storeId
     * @return mixed
     */
    abstract public function init($storeId);

    /**
     *
     * @return mixed
     */
    public function getParams()
    {
        return $this->_config;
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avatax');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Address
     */
    protected function _getAddressHelper()
    {
        return Mage::helper('avatax/address');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getErrorsHelper()
    {
        return Mage::helper('avatax/errors');
    }
}