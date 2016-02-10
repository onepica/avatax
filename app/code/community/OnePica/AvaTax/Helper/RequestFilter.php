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
 * @copyright  Copyright (c) 2016 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class OnePica_AvaTax_Helper_RequestFilter
 */
class OnePica_AvaTax_Helper_RequestFilter extends Mage_Core_Helper_Abstract
{
    /**
     * XMl path to limit get tax request on checkout onepage actions
     */
    const LIMIT_GET_TAX_REQUEST_ON_CHECKOUT_ONEPAGE = 'tax/avatax/limit_gettax_request_on_checkout_onepage_actions';

    /**
     * XML path to limit get tax request on multi shipping checkout actions
     */
    const LIMIT_GET_TAX_REQUEST_ON_MULTISHIPPING_CHECKOUT = 'tax/avatax/limit_gettax_request_on_multishipping_checkout_actions';

    /**
     * Xml path to disable get tax request on unnecessary actions
     */
    const DISABLE_GET_TAX_REQUEST_ON_UNNECESSARY_ACTIONS = 'tax/avatax/limit_gettax_request_on_noncheckout_actions';

    /**
     * Checks if request is filtered
     *
     * @param Mage_Core_Model_Store|int $store
     * @return bool
     */
    public function isRequestFiltered($store)
    {
        $requestPath = $this->_getRequestPath();
        if ($this->_isOnePageCheckoutRequestFiltered($store, $requestPath)) {
            return true;
        }

        if ($this->_isMultishippingCheckoutRequestFiltered($store, $requestPath)) {
            return true;
        }

        if ($this->_isFilteredNonCheckoutActions($store, $requestPath)) {
            return true;
        }

        return false;
    }

    /**
     * Get request path
     *
     * Example: module_name/controller_name/action_name
     *
     * @return string
     */
    protected function _getRequestPath()
    {
        return $this->_getRequest()->getModuleName()
               . '/' . $this->_getRequest()->getControllerName()
               . '/' . $this->_getRequest()->getActionName();
    }

    /**
     * Checks, if one page checkout request are filtering
     *
     * @param Mage_Core_Model_Store|int $store
     * @param string                    $requestPath
     * @return bool
     */
    protected function _isOnePageCheckoutRequestFiltered($store, $requestPath)
    {
        return Mage::getStoreConfigFlag(self::LIMIT_GET_TAX_REQUEST_ON_CHECKOUT_ONEPAGE, $store)
               && in_array($requestPath, $this->_getCheckoutActions(), true);
    }

    /**
     * Get checkout actions
     *
     * @return array
     */
    protected function _getCheckoutActions()
    {
        return array(
            'checkout/onepage/index',
            'checkout/onepage/saveBilling',
            'checkout/onepage/saveShipping',
            'checkout/onepage/saveShippingMethod'
        );
    }

    /**
     * Checks, if multi shipping checkout request are filtering
     *
     * @param Mage_Core_Model_Store|int $store
     * @param string                    $requestPath
     * @return bool
     */
    protected function _isMultishippingCheckoutRequestFiltered($store, $requestPath)
    {
        return Mage::getStoreConfigFlag(self::LIMIT_GET_TAX_REQUEST_ON_MULTISHIPPING_CHECKOUT, $store)
               && in_array($requestPath, $this->_getMultiShippingCheckoutActions(), true);
    }

    /**
     * Get multiple checkout actions
     *
     * @return array
     */
    protected function _getMultiShippingCheckoutActions()
    {
        return array(
            'checkout/multishipping/index',
            'checkout/multishipping/addresses',
            'checkout/multishipping/addressesPost',
            'checkout/multishipping/shipping',
            'checkout/multishipping/shippingPost',
            'checkout/multishipping/billing'
        );
    }

    /**
     * Is filtered request on unnecessary actions
     *
     * @param Mage_Core_Model_Store|int $store
     * @param string                    $requestPath
     * @return bool
     */
    protected function _isFilteredNonCheckoutActions($store, $requestPath)
    {
        return Mage::getStoreConfigFlag(self::DISABLE_GET_TAX_REQUEST_ON_UNNECESSARY_ACTIONS, $store)
               && in_array($requestPath, $this->_getNonCheckoutActions());
    }

    /**
     * Get unnecessary actions
     *
     * @return array
     */
    protected function _getNonCheckoutActions()
    {
        return array(
            'catalog/category/view',
            'customer/account/index'
        );
    }
}
