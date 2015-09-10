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
 * Avatax Observer
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Avalara lib classes
     *
     * @var array
     */
    protected static $_classes = array(
        'TaxRequest',
        'PostTaxRequest',
        'PostTaxResult',
        'CommitTaxRequest',
        'CommitTaxResult',
        'CancelTaxRequest',
        'CancelTaxResult',
        'Enum',
        'CancelCode',
        'ATConfig',
        'ATObject',
        'DynamicSoapClient',
        'AvalaraSoapClient',
        'AddressServiceSoap',
        'Address',
        'Enum',
        'TextCase',
        'Message',
        'SeverityLevel',
        'ValidateRequest',
        'ValidateResult',
        'ValidAddress',
        'TaxServiceSoap',
        'GetTaxRequest',
        'DocumentType',
        'DetailLevel',
        'Line',
        'ServiceMode',
        'GetTaxResult',
        'TaxLine',
        'TaxDetail',
        'PingResult',
        'TaxOverride',
        'TaxOverrideType'
    );

    /**
     * Sets the collectTotals tax node based on the extensions enabled/disabled status
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function salesQuoteCollectTotalsBefore(Varien_Event_Observer $observer)
    {
        $storeId = $observer->getEvent()->getQuote()->getStoreId();
        if (Mage::getStoreConfig('tax/avatax/action', $storeId) != OnePica_AvaTax_Model_Config::ACTION_DISABLE) {
            Mage::getConfig()->setNode('global/sales/quote/totals/tax/class', 'avatax/sales_quote_address_total_tax');
        }
        return $this;
    }

    /**
     * Create a sales invoice record in Avalara
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function salesOrderInvoiceSaveAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();

        if ((int)$invoice->getOrigData('state') !== Mage_Sales_Model_Order_Invoice::STATE_PAID
            && (int)$invoice->getState() === Mage_Sales_Model_Order_Invoice::STATE_PAID
            && Mage::helper('avatax')->isObjectActionable($invoice)
        ) {
            Mage::getModel('avatax_records/queue')
                ->setEntity($invoice)
                ->setType(OnePica_AvaTax_Model_Records_Queue::QUEUE_TYPE_INVOICE)
                ->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_PENDING)
                ->save();
        }

        return $this;
    }

    /**
     * Create a return invoice record in Avalara
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function salesOrderCreditmemoSaveAfter(Varien_Event_Observer $observer)
    {
        /* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if (!$creditmemo->getOrigData($creditmemo->getIdFieldName())
            && Mage::helper('avatax')->isObjectActionable($creditmemo)
        ) {
            Mage::getModel('avatax_records/queue')
                ->setEntity($creditmemo)
                ->setType(OnePica_AvaTax_Model_Records_Queue::QUEUE_TYPE_CREDITMEMEO)
                ->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_PENDING)
                ->save();
        }
        return $this;
    }

    /**
     * Validate addresses when multishipping checkout on set shipping items
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws OnePica_AvaTax_Exception
     */
    public function multishippingSetShippingItems(Varien_Event_Observer $observer)
    {
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();
        $storeId = $quote->getStoreId();

        $errors = array();
        $normalized = false;

        $addresses  = $quote->getAllShippingAddresses();
        $message = Mage::getStoreConfig('tax/avatax/validate_address_message', $storeId);
        foreach ($addresses as $address) {
            /* @var $address OnePica_AvaTax_Model_Sales_Quote_Address */
            if ($address->validate() !== true) {
                $errors[] = sprintf($message, $address->format('oneline'));
            }
            if ($address->getAddressNormalized()) {
                $normalized = true;
            }
        }

        $session = Mage::getSingleton('checkout/session');
        if ($normalized) {
            $session->addNotice(Mage::getStoreConfig('tax/avatax/multiaddress_normalize_message', $storeId));
        }

        if (!empty($errors)) {
            throw new OnePica_AvaTax_Exception(implode('<br />', $errors));
        }
        return $this;
    }

    /**
     * Observer push data to Avalara
     *
     * @return $this;
     */
    public function processQueue()
    {
        Mage::getModel('avatax_records/queue_process')->run();
        return $this;
    }

    /**
     * Test for required values when admin config setting related to the this extension are changed
     *
     * @param Varien_Event_Observer $observer
     * @return bool
     */
    public function adminSystemConfigChangedSectionTax(Varien_Event_Observer $observer)
    {
        Mage::app()->cleanCache('block_html');
        $storeId = $observer->getEvent()->getStore();
        $this->_addErrorsToSession($storeId);
        $this->_addWarningsToSession($storeId);
    }

    /**
     * Observer to clean the log every so often so it does not get too big.
     *
     * @return $this
     */
    public function cleanLog()
    {
        $days = floatval(Mage::getStoreConfig('tax/avatax/log_lifetime'));
        Mage::getModel('avatax_records/log')->deleteLogsByInterval($days);
        return $this;
    }

    /**
     * This an observer function for the event 'controller_front_init_before' and 'default'
     * It prepends our autoloader, so we can load the extra libraries.
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function loadAvaTaxExternalLib(Varien_Event_Observer $observer)
    {
        spl_autoload_register(array($this, 'loadLib'), true, true);
        return $this;
    }

    /**
     * This function can autoloads classes to work with Avalara API
     *
     * @param string $class
     */
    public static function loadLib($class)
    {
        if (in_array($class, self::$_classes)) {
            /** @var OnePica_AvaTax_Helper_Data $helper */
            $helper = Mage::helper('avatax');
            $helper->loadFunctions();
            $helper->loadClass($class);
        }
    }

    /**
     * Set post type for checkout session when 'controller_action_predispatch_checkout_cart_estimatePost' event
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function controllerActionPredispatchCheckoutCartEstimatePost(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setPostType('estimate');
        return $this;
    }

    /**
     * Set post type for checkout session when 'controller_action_predispatch_checkout_onepage_index' event
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function controllerActionPredispatchCheckoutOnepageIndex(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setPostType('onepage');
        return $this;
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
        if (strpos(Mage::getStoreConfig('tax/avatax/url', $storeId), 'development.avalara.net') !== false) {
            $warnings[] = Mage::helper('avatax')->__(
                'You are using the AvaTax development connection URL. If you are receiving errors about authentication, please ensure that you have a development account.'
            );
        }
        if (Mage::getStoreConfig('tax/avatax/action', $storeId) == OnePica_AvaTax_Model_Config::ACTION_DISABLE) {
            $warnings[] = Mage::helper('avatax')->__('All AvaTax services are disabled');
        }
        if (Mage::getStoreConfig('tax/avatax/action', $storeId) == OnePica_AvaTax_Model_Config::ACTION_CALC) {
            $warnings[] = Mage::helper('avatax')->__('Orders will not be sent to the AvaTax system');
        }
        if (Mage::getStoreConfig('tax/avatax/action', $storeId) == OnePica_AvaTax_Model_Config::ACTION_CALC_SUBMIT) {
            $warnings[] = Mage::helper('avatax')->__('Orders will be sent but never committed to the AvaTax system');
        }
        if (!Mage::getResourceModel('cron/schedule_collection')->count()) {
            $warnings[] = Mage::helper('avatax')->__(
                'It appears that Magento\'s cron scheduler is not running. For more information, see %s.',
                '<a href="http://www.magentocommerce.com/wiki/how_to_setup_a_cron_job" target="_black">How to Set Up a Cron Job</a>'
            );
        }
        if ($this->_isRegionFilterAll() && $this->_canNotBeAddressValidated()) {
            $warnings[] = Mage::helper('avatax')->__('Please be aware that address validation will not work for addresses outside United States and Canada');
        }

        return $warnings;
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
            $this->_checkConnectionFields($storeId),
            $this->_checkSkuFields($storeId),
            $this->_checkSoapSupport(),
            $this->_checkSslSupport()
        );

        return $errors;
    }

    /**
     * Get adminhtml model session
     *
     * @return \Mage_Adminhtml_Model_Session
     */
    protected function _getAdminhtmlSession()
    {
        return Mage::getSingleton('adminhtml/session');
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
     * Send ping request
     *
     * @param int $storeId
     * @return array
     */
    protected function _sendPing($storeId)
    {
        $errors = array();
        $ping = Mage::getSingleton('avatax/avatax_ping')->ping($storeId);
        if ($ping !== true) {
            $errors[] = $ping;
        }

        return $errors;
    }

    /**
     * Check connection fields
     *
     * @param int $storeId
     * @return array
     */
    protected function _checkConnectionFields($storeId)
    {
        $errors = array();
        if (!Mage::getStoreConfig('tax/avatax/url', $storeId)) {
            $errors[] = Mage::helper('avatax')->__('You must enter a connection URL');
        }
        if (!Mage::getStoreConfig('tax/avatax/account', $storeId)) {
            $errors[] = Mage::helper('avatax')->__('You must enter an account number');
        }
        if (!Mage::getStoreConfig('tax/avatax/license', $storeId)) {
            $errors[] = Mage::helper('avatax')->__('You must enter a license key');
        }
        if (!is_numeric(Mage::getStoreConfig('tax/avatax/log_lifetime'))) {
            $errors[] = Mage::helper('avatax')->__('You must enter the number of days to keep log entries');
        }
        if (!Mage::getStoreConfig('tax/avatax/company_code', $storeId)) {
            $errors[] = Mage::helper('avatax')->__('You must enter a company code');
        }

        return $errors;
    }

    /**
     * Check Sku fields
     *
     * @param int $storeId
     * @return array
     */
    protected function _checkSkuFields($storeId)
    {
        $errors = array();
        if (!Mage::getStoreConfig('tax/avatax/shipping_sku', $storeId)) {
            $errors[] = Mage::helper('avatax')->__('You must enter a shipping sku');
        }
        if (!Mage::getStoreConfig('tax/avatax/adjustment_positive_sku', $storeId)) {
            $errors[] = Mage::helper('avatax')->__('You must enter an adjustment refund sku');
        }
        if (!Mage::getStoreConfig('tax/avatax/adjustment_negative_sku', $storeId)) {
            $errors[] = Mage::helper('avatax')->__('You must enter an adjustment fee sku');

            return $errors;
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
        return (int)$this->_getDataHelper()->getRegionFilterModByCurrentScope()
               === OnePica_AvaTax_Model_Config::REGIONFILTER_ALL;
    }

    /**
     * Can not be address validated
     *
     * @return array
     */
    protected function _canNotBeAddressValidated()
    {
        return (bool)array_diff(
            $this->_getDataHelper()->getTaxableCountryByCurrentScope(),
            $this->_getDataHelper()->getAddressValidationCountries()
        );
    }

    /**
     * Get data helper
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getDataHelper()
    {
        return Mage::helper('avatax');
    }
}
