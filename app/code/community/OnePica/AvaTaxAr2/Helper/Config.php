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
 * The base AvaTaxAr2 Config Helper class
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Helper_Config extends Mage_Core_Helper_Abstract
{
    const XML_PATH_TO_ACTION = 'tax/avatax_document_management/action';

    const XML_PATH_TO_ENV = 'tax/avatax_document_management/env';

    const XML_PATH_TO_ACCOUNT_NUMBER = 'tax/avatax_document_management/account';

    const XML_PATH_TO_KEY = 'tax/avatax_document_management/license';

    /**
     * Is AvaTaxAr2 enabled.
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isEnabled()
    {
        $websiteId = Mage::app()->getRequest()->get('website');
        $storeId = Mage::app()->getRequest()->get('store');

        if ($websiteId && !$storeId) {
            return (bool)Mage::app()->getWebsite($websiteId)->getConfig(self::XML_PATH_TO_ACTION);
        }

        return $this->getStatusServiceAction($storeId);
    }

    /**
     * Get avatax status
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getStatusServiceAction($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_ACTION, $storeId);
    }

    /**
     * Returns service environment
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getServiceEnv($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_ENV, $store);
    }

    /**
     * Returns service account id
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getServiceAccountId($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_ACCOUNT_NUMBER, $store);
    }

    /**
     * Returns service account id
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getServiceKey($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_KEY, $store);
    }
}
