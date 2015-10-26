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
     * Transaction type sale
     */
    const TRANSACTION_TYPE_SALE = 'Sale';

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
        if ($result->getHasError() === false) {
            switch ($this->_getHelper()->getLogMode($storeId)) {
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

        if (in_array($type, $this->_getHelper()->getLogType($storeId))) {
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

    /**
     * Get date model
     *
     * @return Mage_Core_Model_Date
     */
    protected function _getDateModel()
    {
        return Mage::getSingleton('core/date');
    }
}
