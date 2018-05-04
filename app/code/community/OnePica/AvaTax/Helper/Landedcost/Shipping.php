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
 * Class OnePica_AvaTax_Helper_LandedCost_Shipping
 */
class OnePica_AvaTax_Helper_Landedcost_Shipping extends Mage_Core_Helper_Abstract
{
    protected $_shippingMethods = array();

    protected $_configData = array();

    protected $_valuesSelect = array();

    const AVATAX_LANDEDCOST_NONE = null;

    const AVATAX_LANDEDCOST_GROUND = 'ground';

    const AVATAX_LANDEDCOST_AIR = 'air';

    const AVATAX_LANDEDCOST_OCEAN = 'ocean';

    /**
     * Returns all available shipping methods
     *
     * @return OnePica_AvaTax_Model_Landedcost_Shipping_Method[]
     * @throws \Varien_Exception
     */
    public function getAllShippingMethods()
    {
        /** @var Mage_Shipping_Model_Carrier_Abstract[] $allMethods */
        $allMethods = Mage::getSingleton('shipping/config')->getAllCarriers();

        if (empty($this->_shippingMethods)) {
            /**
             * Collect shipping methods
             *
             * @var  $shippingCode  string
             * @var  $shippingModel Mage_Shipping_Model_Carrier_Abstract
             */
            foreach ($allMethods as $shippingCode => $shippingModel) {
                $this->_shippingMethods[] = Mage::getModel(
                    'avatax/landedcost_shipping_method', array(
                        'id'    => $shippingCode,
                        'model' => $shippingModel
                    )
                );
            }
        }

        return $this->_shippingMethods;
    }

    /**
     * Load Shipping Method with Carrier Code
     * @param $codeCarrier
     * @return mixed
     */
    public function getShippingMethodByCode($codeCarrier)
    {
        $shippingModel = Mage::getSingleton('shipping/config')->getCarrierInstance($codeCarrier);
        Mage::getModel(
            'avatax/landedcost_shipping_method', array(
                'id'    => $codeCarrier,
                'model' => $shippingModel
            )
        );

        return $shippingModel;
    }

    /**
     * Retrive config data
     *
     * @return array
     */
    public function getConfigData()
    {
        if (empty($this->_configData)) {
            $this->_configData = Mage::getStoreConfig('tax/avatax_landed_cost_shipping');
        }

        return $this->_configData;
    }

    /**
     * Prepare current data for select field
     *
     * @param string $path
     * @return string
     */
    public function getConfigFormData($path = '')
    {
        $formData = '';
        if ($path && isset($this->getConfigData()[$path])) {
            $formData = $this->getConfigData()[$path];
        }

        return $formData;
    }


    /**
     * Defines select labels and values
     *
     * @param bool $isMultiSelect
     * @return array
     */
    public function getSelectValues($isMultiSelect = false)
    {
        if (empty($this->_valuesSelect)) {
            $this->_valuesSelect = array(
                array('label' => Mage::helper('adminhtml')->__(''), 'value' => self::AVATAX_LANDEDCOST_NONE),
                array('label' => Mage::helper('adminhtml')->__('Ground'), 'value' => self::AVATAX_LANDEDCOST_GROUND),
                array('label' => Mage::helper('adminhtml')->__('Air'), 'value' => self::AVATAX_LANDEDCOST_AIR),
                array('label' => Mage::helper('adminhtml')->__('Ocean'), 'value' => self::AVATAX_LANDEDCOST_OCEAN)
            );
        }

        $result = array();
        foreach ($this->_valuesSelect as $item) {
            if ($isMultiSelect && $item['value'] === self::AVATAX_LANDEDCOST_NONE) {
                continue;
            }

            array_push($result, $item);
        }

        return $result;
    }

    /**
     * @param \Mage_Sales_Model_Quote_Address $address
     * @return null|string
     */
    public function getShippingMode($address)
    {
        $shippingMode = null;
        /** @var Mage_Sales_Model_Quote_Address_Rate $rate */
        $rate = $address->getShippingRateByCode($address->getShippingMethod());

        if ($rate) {
            $shippingMethod = $this->getShippingMethodByCode($rate->getCarrier());
            $isMultiSelect = $shippingMethod->getCarrierMethods() !== null;
            if ($isMultiSelect) {
                foreach ($this->getSelectValues($isMultiSelect) as $selectValue) {
                    $multiselectConfigData = explode(
                        ',',
                        $this->getConfigFormData($rate->getCarrier() . '_' . $selectValue['value'])
                    );

                    if (isset($multiselectConfigData) && in_array($rate->getMethod(), $multiselectConfigData)) {
                        $shippingMode = $selectValue['value'];
                    }
                }
            } else {
                $shippingMode = $this->getConfigFormData($rate->getCarrier());
            }
        } else {
            $shippingMode = self::AVATAX_LANDEDCOST_NONE;
        }

        return $shippingMode;
    }
}
