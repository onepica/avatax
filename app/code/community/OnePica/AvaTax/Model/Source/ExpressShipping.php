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
 * Express shipping methods source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Source_ExpressShipping
{
    /**
     * Gets the list of type for the admin config multiselect
     *
     * @return array
     * @throws OnePica_AvaTax_Exception
     * @throws \Varien_Exception
     */
    public function toOptionArray()
    {
        $options = array();

        foreach ($this->_getShippingMethods() as $shippingMethod) {
            $options[] = array(
                'label' => $shippingMethod->getTitle(),
                'value' => $this->_prepareCarrierList($shippingMethod)
            );
        }

        return $options;
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

    /**
     * Prepare carrier list.
     *
     * @param OnePica_AvaTax_Model_Landedcost_Shipping_Method $shippingMethod
     * @return array
     */
    protected function _prepareCarrierList($shippingMethod)
    {
        $carrierMethods = $shippingMethod->getCarrierMethods();

        if (!$carrierMethods) {
            return array(
                array(
                    'title' => $shippingMethod->getTitle(),
                    'label' => $shippingMethod->getTitle(),
                    'value' => $shippingMethod->getId() . '_' . $shippingMethod->getId()
                )
            );
        }

        $carriers = array();

        foreach ($carrierMethods as $carrierMethod) {
            $carriers[] = array(
                'title' => $carrierMethod['label'],
                'label' => $carrierMethod['label'],
                'value' => $shippingMethod->getId() . '_' . $carrierMethod['value']
            );
        }

        return $carriers;
    }

    /**
     * @return \OnePica_AvaTax_Model_Landedcost_Shipping_Method[]
     * @throws \Varien_Exception
     */
    protected function _getShippingMethods()
    {
        return $this->_getShippingHelper()->getAllShippingMethods();
    }

    /**
     * @return \OnePica_AvaTax_Helper_Landedcost_Shipping
     */
    protected function _getShippingHelper()
    {
        return Mage::helper('avatax/landedcost_shipping');
    }
}
