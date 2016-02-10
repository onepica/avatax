<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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
 * The base AvaTax Helper class
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check if avatax extension is enabled
     *
     * @param null|bool|int|Mage_Core_Model_Store $store $store
     * @return bool
     */
    public function isServiceEnabled($store = null)
    {
        return ($this->_getConfigData()->getStatusServiceAction($store)
                != OnePica_AvaTax_Model_Service_Abstract_Config::ACTION_DISABLE);
    }

    /**
     * Is avatax 16 service type
     *
     * @return bool
     */
    public function isAvatax16()
    {
        return $this->_getConfigData()->getActiveService() === OnePica_AvaTax_Helper_Config::AVATAX16_SERVICE_TYPE;
    }

    /**
     * Is avatax service type
     *
     * @return bool
     */
    public function isAvatax()
    {
        return $this->_getConfigData()->getActiveService() === OnePica_AvaTax_Helper_Config::AVATAX_SERVICE_TYPE;
    }

    /**
     * Gets the documenation url
     *
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'http://www.onepica.com/magento-extensions/avatax/';
    }

    /**
     * Is development mod
     *
     * @param int $storeId
     * @return bool
     */
    public function isDevMod($storeId)
    {
        $serviceUrl = $this->_getConfigData()->getServiceUrl($storeId);

        return ($this->isAvatax()
                && ($serviceUrl === OnePica_AvaTax_Model_Source_Avatax_Url::DEVELOPMENT_URL))
               || ($this->isAvatax16()
                   && ($serviceUrl === OnePica_AvaTax_Model_Source_Avatax16_Url::DEVELOPMENT_URL));
    }

    /**
     * Returns the logging level
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return int
     */
    public function getLogMode($store = null)
    {
        return $this->_getConfigData()->getConfigLogMode($store);
    }

    /**
     * Returns the logging type
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getLogType($store = null)
    {
        return explode(",", $this->_getConfigData()->getLogTypeList($store));
    }

    /**
     * Does any store have this extension disabled?
     *
     * @return bool
     */
    public function isAnyStoreDisabled()
    {
        $disabled = false;
        $storeCollection = Mage::app()->getStores();

        foreach ($storeCollection as $store) {
            $disabled |= $this->_getConfigData()->getStatusServiceAction($store->getId())
                         == OnePica_AvaTax_Model_Service_Abstract_Config::ACTION_DISABLE;
        }

        return $disabled;
    }

    /**
     * Create Zend_Date object with date converted to store timezone and store Locale
     * This method from Mage_Core_Model_Locale.
     * This need for backward compatibility with older magento versions which not have 4th parameter in this method
     *
     * @param   mixed                               $store       Information about store
     * @param   string|integer|Zend_Date|array|null $date        date in UTC
     * @param   boolean                             $includeTime flag for including time to date
     * @param   string|null                         $format
     * @return  Zend_Date
     */
    public function storeDate($store = null, $date = null, $includeTime = false, $format = null)
    {
        $timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
        $date = new Zend_Date($date, $format, Mage::app()->getLocale()->getLocale());
        $date->setTimezone($timezone);
        if (!$includeTime) {
            $date->setHour(0)
                ->setMinute(0)
                ->setSecond(0);
        }

        return $date;
    }

    /**
     * Adds a comment to order history. Method chosen based on Magento version.
     *
     * @param Mage_Sales_Model_Order $order
     * @param string                 $comment
     * @return $this
     */
    public function addStatusHistoryComment($order, $comment)
    {
        if (method_exists($order, 'addStatusHistoryComment')) {
            $order->addStatusHistoryComment($comment)->save();
        } elseif (method_exists($order, 'addStatusToHistory')) {
            $order->addStatusToHistory($order->getStatus(), $comment, false)->save();
        }

        return $this;
    }

    /**
     * Round up
     *
     * @param float $value
     * @param int   $precision
     * @return float
     */
    public function roundUp($value, $precision)
    {
        $fact = pow(10, $precision);

        return ceil($fact * $value) / $fact;
    }

    /**
     * Get config helper
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    private function _getConfigData()
    {
        return Mage::helper('avatax/config');
    }

    /**
     * Generates client name for requests
     * Parts:
     * - MyERP: the ERP that this connector is for (not always applicable)
     * - Majver: version info for the ERP (not always applicable)
     * - MinVer: version info for the ERP (not always applicable)
     * - MyConnector: Name of the OEM's connector AND the name of the OEM (company)  *required*
     * - Majver: OEM's connector version *required*
     * - MinVer: OEM's connector version *required*
     *
     * @example Magento,1.4,.0.1,OP_AvaTax by One Pica,2,0.1
     * @return string
     */
    public function getClientName()
    {
        $mageVersion = Mage::getVersion();
        $mageVerParts = explode('.', $mageVersion, 2);

        $opVersion = Mage::getResourceModel('core/resource')->getDbVersion('avatax_records_setup');
        $opVerParts = explode('.', $opVersion, 2);

        $part = array();
        $part[] = OnePica_AvaTax_Model_Service_Abstract_Config::CONFIG_KEY;
        $part[] = $mageVerParts[0];
        $part[] = $mageVerParts[1];
        $part[] = OnePica_AvaTax_Model_Service_Abstract_Config::APP_NAME;
        $part[] = $opVerParts[0];
        $part[] = $opVerParts[1];

        return implode(',', $part);
    }
}
