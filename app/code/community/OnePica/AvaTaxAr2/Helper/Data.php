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
 * The base AvaTaxAr2 Helper class
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Helper_Data extends Mage_Core_Helper_Abstract
{
    const AVATAX_CUSTOMER_DOCUMENTS_FORM_CODE = 'customer_avatax_exempt';

    /**
     * Generates app name for Rest V2 requests
     *
     * @example OP_AvaTax by One Pica
     * @return string
     */
    public function getAppName()
    {
        return OnePica_AvaTax_Model_Service_Abstract_Config::APP_NAME;
    }

    /**
     * Generates app version for Rest V2 requests
     *
     * @example 3.7.0.0
     * @return string
     */
    public function getAppVersion()
    {
        $opVersion = Mage::getResourceModel('core/resource')->getDbVersion('avatax_records_setup');

        return $opVersion;
    }

    /**
     * Generates machine name for Rest V2 requests
     *
     * @example Linux,5.6.30-1
     * @return string
     */
    public function getMachineName()
    {
        $nameParams = array(PHP_OS, PHP_VERSION);

        return implode(',', $nameParams);
    }

    /**
     * @param \Mage_Customer_Model_Customer $customer
     * @return string
     */
    public function getCustomerNumber($customer)
    {
        $store = $customer->getStore();
        $customerNumber = $customer->getData($this->getConfig()->getCustomerCodeFormatAttribute($store));
        if (!$customerNumber) {
            $customerNumber = $this->generateCustomerNumber($customer);
        }

        return $customerNumber;
    }

    /**
     * @param $customer
     * @param $customerNumber
     * @return mixed
     */
    public function getCustomerNumberEx($customer, $customerNumber = null)
    {
        $store = $customer->getStore();
        $result = $customer->getData($this->getConfig()->getCustomerCodeFormatAttribute($store), $customerNumber);
        return $result;
    }

    /**
     * @param $customer
     * @param $customerNumber
     * @return mixed
     */
    public function setCustomerNumberEx($customer, $customerNumber)
    {
        $store = $customer->getStore();
        return $customer->setData($this->getConfig()->getCustomerCodeFormatAttribute($store), $customerNumber);
    }

    /**
     * Config
     *
     * @return OnePica_AvaTaxAr2_Helper_Config
     */
    public function getConfig()
    {
        return Mage::helper('avataxar2/config');
    }

    /**
     * Generate Customer Number
     *
     * @param $customer
     * @return mixed
     */
    public function generateCustomerNumber($customer)
    {
        return $customer->getEmail();
    }
}
