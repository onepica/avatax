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
 * Avatax 16 service abstract model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
abstract class OnePica_AvaTax_Model_Service_Avatax16_Abstract
{
    /**
     * The module helper
     *
     * @var OnePica_AvaTax_Helper_Data
     */
    protected $_helper = null;

    /**
     * The module address helper
     *
     * @var OnePica_AvaTax_Helper_Address
     */
    protected $_addressHelper = null;

    /**
     * The module config helper
     *
     * @var OnePica_AvaTax_Helper_Config
     */
    protected $_configHelper = null;

    /**
     * The module config helper
     *
     * @var OnePica_AvaTax_Helper_Errors
     */
    protected $_errorsHelper = null;

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getHelper()
    {
        if (!$this->_helper) {
            $this->_helper = Mage::helper('avatax');
        }
        return $this->_helper;
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Address
     */
    protected function _getAddressHelper()
    {
        if (!$this->_addressHelper) {
            $this->_addressHelper = Mage::helper('avatax/address');
        }
        return $this->_addressHelper;
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        if (!$this->_configHelper) {
            $this->_configHelper = Mage::helper('avatax/config');
        }
        return $this->_configHelper;
    }

    /**
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getErrorsHelper()
    {
        if (!$this->_errorsHelper) {
            $this->_errorsHelper = Mage::helper('avatax/errors');
        }
        return $this->_errorsHelper;
    }

    /**
     * Logs a debug message
     *
     * @param string $type
     * @param string $request the request string
     * @param string $result the result string
     * @param int $storeId id of the store the call is make for
     * @param mixed $additional any other info
     * @return $this
     */
    protected function _log($type, $request, $result, $storeId = null, $additional = null)
    {
        if ($result->getHasError() === true) {
            switch ($this->getHelper()->getLogMode($storeId)) {
                case OnePica_AvaTax_Model_Source_Logmode::ERRORS:
                    return $this;
                    break;
                case OnePica_AvaTax_Model_Source_Logmode::NORMAL:
                    $additional = null;
                    break;
            }
        }
        $level = $result->getHasError() ? OnePica_AvaTax_Model_Records_Log::LOG_LEVEL_ERROR
            : OnePica_AvaTax_Model_Records_Log::LOG_LEVEL_SUCCESS;

        if (in_array($type, $this->getHelper()->getLogType($storeId))) {
            Mage::getModel('avatax_records/log')
                ->setStoreId($storeId)
                ->setLevel($level)
                ->setType($type)
                ->setRequest(print_r($request, true))
                ->setResult(print_r($result, true))
                ->setAdditional($additional)
                ->save();
        }
        return $this;
    }
}
