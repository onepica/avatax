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
 * @copyright  Copyright (c) 2016 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class OnePica_AvaTax_Model_Tax_Calculation
 */
class OnePica_AvaTax_Model_Tax_Calculation extends Mage_Tax_Model_Calculation
{
    /**
     * Get calculation tax rate by specific request
     *
     * @param   Varien_Object $request
     * @return  float
     */
    public function getRate($request)
    {
        if ($this->_getDataHelper()->isServiceEnabled($this->_getStore($request))) {
            return 0;
        }

        return parent::getRate($request);
    }

    /**
     * Get avatax data helper
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getDataHelper()
    {
        return Mage::helper('avatax');
    }

    /**
     * Get information about tax rates applied to request
     *
     * @param   Varien_Object $request
     * @return  array
     */
    public function getAppliedRates($request)
    {
        if ($this->_getDataHelper()->isServiceEnabled($this->_getStore($request))) {
            return array();
        }

        return parent::getAppliedRates($request);
    }

    /**
     * Get store
     *
     * @param Varien_Object $request
     * @return \Mage_Core_Model_Store
     */
    protected function _getStore($request)
    {
        $store = Mage::app()->getStore();
        if ($request->getStore() !== null) {
            $store = $request->getStore();
        }

        return $store;
    }
}
