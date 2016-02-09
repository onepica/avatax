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
 * Avatax Observer AdminSystemConfigChangedSectionTax
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_AdminSystemConfigChangedSectionTax extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Test for required values when admin config setting related to the this extension are changed
     *
     * @param Varien_Event_Observer $observer
     * @return bool
     */
    public function execute(Varien_Event_Observer $observer)
    {
        Mage::app()->cleanCache('block_html');
        $storeId = $observer->getEvent()->getStore();
        $this->_addErrorsToSession($storeId);
        $this->_addWarningsToSession($storeId);
    }

    /**
     * Add error messages to session
     *
     * @param int $storeId
     * @return $this
     */
    protected function _addErrorsToSession($storeId)
    {
        $session = $this->_getAdminhtmlSession();
        $errors = $this->_prepareErrors($storeId);
        if (count($errors) == 1) {
            $session->addError(implode('', $errors));
        } elseif (count($errors)) {
            $session->addError(
                Mage::helper('avatax')->__('Please fix the following issues:') . '<br /> - '
                . implode('<br /> - ', $errors)
            );
        }

        return $this;
    }

    /**
     * Add warning messages to session
     *
     * @param int $storeId
     * @return $this
     */
    protected function _addWarningsToSession($storeId)
    {
        $session = $this->_getAdminhtmlSession();
        $warnings = $this->_prepareWarnings($storeId);
        if (count($warnings) == 1) {
            $session->addWarning(implode('', $warnings));
        } elseif (count($warnings)) {
            $session->addWarning(
                Mage::helper('avatax')->__('Please be aware of the following warnings:')
                . '<br /> - '
                . implode('<br /> - ', $warnings)
            );
        }

        return $this;
    }

    /**
     * Prepare errors array
     *
     * @param int $storeId
     * @return array
     */
    protected function _prepareErrors($storeId)
    {
        $errors = array();
        $errors = array_merge(
            $errors,
            $this->_sendPing($storeId),
            $this->_checkSoapSupport(),
            $this->_checkSslSupport()
        );

        return $errors;
    }

    /**
     * Prepare warnings array
     *
     * @param int $storeId
     * @return array
     */
    protected function _prepareWarnings($storeId)
    {
        $warnings = array();

        if (strpos($this->_getConfigHelper()->getServiceUrl($storeId), 'development.avalara.net') !== false) {
            $warnings[] = Mage::helper('avatax')->__(
                'You are using the AvaTax development connection URL. If you are receiving errors about authentication, please ensure that you have a development account.'
            );
        }

        if ($this->_getConfigHelper()->getStatusServiceAction($storeId)
            == OnePica_AvaTax_Model_Service_Abstract_Config::ACTION_DISABLE
        ) {
            $warnings[] = Mage::helper('avatax')->__('All AvaTax services are disabled');
        }

        if ($this->_getConfigHelper()->getStatusServiceAction($storeId)
            == OnePica_AvaTax_Model_Service_Abstract_Config::ACTION_CALC
        ) {
            $warnings[] = Mage::helper('avatax')->__('Orders will not be sent to the AvaTax system');
        }

        if ($this->_getDataHelper()->isAvatax() &&
            ($this->_getConfigHelper()->getStatusServiceAction($storeId)
                == OnePica_AvaTax_Model_Service_Abstract_Config::ACTION_CALC_SUBMIT)
        ) {
            $warnings[] = Mage::helper('avatax')->__('Orders will be sent but never committed to the AvaTax system');
        }

        if (!Mage::getResourceModel('cron/schedule_collection')->count()) {
            $warnings[] = Mage::helper('avatax')->__(
                'It appears that Magento\'s cron scheduler is not running. For more information, see %s.',
                '<a href="http://www.magentocommerce.com/wiki/how_to_setup_a_cron_job" target="_black">How to Set Up a Cron Job</a>'
            );
        }

        if ($this->_isRegionFilterAll() && $this->_canNotBeAddressValidated()) {
            $warnings[] = Mage::helper('avatax')
                ->__('Please be aware that address validation will not work for addresses outside United States and Canada');
        }

        return $warnings;
    }

    /**
     * Send ping request
     *
     * @param int $storeId
     * @return array
     */
    protected function _sendPing($storeId)
    {
        $errors = array();
        $ping = Mage::getSingleton('avatax/action_ping')->ping($storeId);
        if ($ping !== true) {
            $errors[] = $ping;
        }

        return $errors;
    }

    /**
     * Check SOAP support
     *
     * @return array
     */
    protected function _checkSoapSupport()
    {
        $errors = array();
        if (!class_exists('SoapClient')) {
            $errors[] = Mage::helper('avatax')->__(
                'The PHP class SoapClient is missing. It must be enabled to use this extension. See %s for details.',
                '<a href="http://www.php.net/manual/en/book.soap.php" target="_blank">http://www.php.net/manual/en/book.soap.php</a>'
            );
        }

        return $errors;
    }

    /**
     * Check SSL support
     *
     * @return array
     */
    protected function _checkSslSupport()
    {
        $errors = array();
        if (!function_exists('openssl_sign') && count($errors)) {
            $key = array_search(Mage::helper('avatax')->__('SSL support is not available in this build'), $errors);
            if (isset($errors[$key])) {
                unset($errors[$key]);
            }
            $errors[] = Mage::helper('avatax')->__(
                'SSL must be enabled in PHP to use this extension. Typically, OpenSSL is used but it is not enabled on your server. This may not be a problem if you have some other form of SSL in place. For more information about OpenSSL, see %s.',
                '<a href="http://www.php.net/manual/en/book.openssl.php" target="_blank">http://www.php.net/manual/en/book.openssl.php</a>'
            );
        }

        return $errors;
    }

    /**
     * Is region filter all mod
     *
     * @return bool
     */
    protected function _isRegionFilterAll()
    {
        return (int)Mage::helper('avatax/address')->getRegionFilterModByCurrentScope()
               === OnePica_AvaTax_Model_Service_Abstract_Config::REGIONFILTER_ALL;
    }

    /**
     * Can not be address validated
     *
     * @return array
     */
    protected function _canNotBeAddressValidated()
    {
        return (bool)array_diff(
            Mage::helper('avatax/address')->getTaxableCountryByCurrentScope(),
            Mage::helper('avatax/address')->getAddressValidationCountries()
        );
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
