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
 * Class OnePica_AvaTax_Model_Calculator
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */

class OnePica_AvaTax_Model_Calculator
{
    /**
     * Service
     *
     * @var mixed
     */
    protected $_service;

    /**
     * Construct
     *
     * @todo implement logic to init correct service
     */
    public function __construct()
    {
        // init service
        $this->_service = new OnePica_AvaTax_Model_Service_Avatax();
    }

    /**
     * Get Service
     *
     * return mixed
     */
    protected function _getService()
    {
        return $this->_service;
    }

    /**
     * Get rates from Service
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return string
     */
    protected function _getRates($item)
    {
        return $this->_getService()->getRates($item);
    }

    /**
     * Get rates data
     * An array of rates that acts as a cache
     * Example: $_rates[$cachekey] = array(
     *     'timestamp' => 1325015952
     *     'summary' => array(
     *         array('name'=>'NY STATE TAX', 'rate'=>4, 'amt'=>6),
     *         array('name'=>'NY CITY TAX', 'rate'=>4.50, 'amt'=>6.75),
     *         array('name'=>'NY SPECIAL TAX', 'rate'=>4.375, 'amt'=>0.56)
     *     ),
     *     'items' => array(
     *         5 => array('rate'=>8.875, 'amt'=>13.31),
     *         'Shipping' => array('rate'=>0, 'amt'=>0)
     *     )
     * )
     * @return array
     */
    protected function _getRatesData()
    {
        return $this->_getService()->getRatesData();
    }

    /**
     * Estimates tax rate for one item.
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return int
     */
    public function getItemRate($item)
    {
        if ($this->isProductCalculated($item)) {
            return 0;
        } else {
            $key = $this->_getRates($item);
            $id = $item->getSku();
            $ratesData = $this->_getRatesData();
            return isset($ratesData[$key]['items'][$id]['rate']) ? $ratesData[$key]['items'][$id]['rate'] : 0;
        }
    }

    /**
     * Get item tax
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return int
     */
    public function getItemGiftTax($item)
    {
        if ($item->getParentItemId()) {
            return 0;
        }
        $key = $this->_getRates($item);
        $ratesData = $this->_getRatesData();
        $id = $item->getSku();
        return isset($ratesData[$key]['gw_items'][$id]['amt']) ? $ratesData[$key]['gw_items'][$id]['amt'] : 0;
    }

    /**
     * Estimates tax amount for one item. Does not trigger a call if the shipping
     * address has no postal code, or if the postal code is set to "-" (OneStepCheckout)
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return int
     */
    public function getItemTax($item)
    {
        if ($item->getAddress()->getPostcode() && $item->getAddress()->getPostcode() != '-') {
            if ($this->isProductCalculated($item)) {
                $tax = 0;
                foreach ($item->getChildren() as $child) {
                    $child->setAddress($item->getAddress());
                    $tax += $this->getItemTax($child);
                }
                return $tax;
            } else {
                $key = $this->_getRates($item);
                $ratesData = $this->_getRatesData();
                $id = $item->getSku();
                return isset($ratesData[$key]['items'][$id]['amt']) ? $ratesData[$key]['items'][$id]['amt'] : 0;
            }
        }
        return 0;
    }

    /**
     * Get tax detail summary
     *
     * @param int|null $addressId
     * @return array
     */
    public function getSummary($addressId = null)
    {
        $summary = null;
        $ratesData = $this->_getRatesData();
        if ($addressId) {
            $timestamp = 0;
            foreach ($ratesData as $row) {
                if (isset($row['address_id']) && $row['address_id'] == $addressId && $row['timestamp'] > $timestamp) {
                    $summary = $row['summary'];
                    $timestamp = $row['timestamp'];
                }
            }
        }

        if ($summary === null) {
            $requestKey = Mage::getSingleton('avatax/session')->getLastRequestKey();
            $summary = isset($ratesData[$requestKey]['summary']) ? $ratesData[$requestKey]['summary'] : array();
        }

        return $summary;
    }

    /**
     * Test to see if the product carries its own numbers or is calculated based on parent or children
     *
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item|mixed $item
     * @return bool
     */
    public function isProductCalculated($item)
    {
        // check if item has methods as far as shipping, gift wrapping, printed card item comes as Varien_Object
        if (method_exists($item, 'isChildrenCalculated') && method_exists($item, 'getParentItem')) {
            if ($item->isChildrenCalculated() && !$item->getParentItem()) {
                return true;
            }
            if (!$item->isChildrenCalculated() && $item->getParentItem()) {
                return true;
            }
        }
        return false;
    }
}
