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
 * Class OnePica_AvaTax_Model_Action_Calculator
 */
class OnePica_AvaTax_Model_Action_Calculator extends OnePica_AvaTax_Model_Action_Abstract
{
    /**
     * Address parameter
     */
    const ADDRESS_PARAMETER = 'address';

    /**
     * Address
     *
     * @var Mage_Sales_Model_Quote_Address
     */
    protected $_address;

    /**
     * Rates
     *
     * @var array
     */
    protected $_rates = array();

    /**
     * OnePica_AvaTax_Model_Calculator constructor.
     *
     * @param array $params
     * @throws \OnePica_AvaTax_Exception
     */
    public function __construct($params = array())
    {
        parent::__construct($params);
        $this->_checkAddress($params);
        $this->initRates($params[self::ADDRESS_PARAMETER]);
    }

    /**
     * Init rates
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function initRates($address)
    {
        $this->_address = $address;
        $this->setStoreId($address->getQuote()->getStoreId());
        $this->_rates = $this->_getService()->getRates($address);

        if (isset($this->_rates['failure']) && ($this->_rates['failure'] === true)) {
            // set error flag for processing estimation errors on upper level
            $this->_address->getQuote()->setData('estimate_tax_error', true);
        }

        return $this;
    }

    /**
     * Get rates from Service
     * Example: $_ratesData = array(
     *     'timestamp' => 1325015952
     *     'summary' => array(
     *         array('name'=>'NY STATE TAX', 'rate'=>4, 'amt'=>6),
     *         array('name'=>'NY CITY TAX', 'rate'=>4.50, 'amt'=>6.75),
     *         array('name'=>'NY SPECIAL TAX', 'rate'=>4.375, 'amt'=>0.56)
     *     ),
     *     'items' => array(
     *         5 => array('rate'=>8.875, 'amt'=>13.31),
     *         'Shipping' => array('rate'=>0, 'amt'=>0)
     *     ),
     *    // if error on get tax
     *     'failure' => true
     * )
     *
     * @return array
     */
    protected function _getRates()
    {
        return $this->_rates;
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
            $id = $item->getId();
            $ratesData = $this->_getRates();

            return isset($ratesData['items'][$id]['rate']) ? $ratesData['items'][$id]['rate'] : 0;
        }
    }

    /**
     * Get item taxable
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return int
     */
    public function getItemTaxable($item)
    {
        if ($this->isProductCalculated($item)) {
            return 0;
        }

        $id = $item->getId();
        $ratesData = $this->_getRates();

        return isset($ratesData['items'][$id]['taxable']) ? $ratesData['items'][$id]['taxable'] : 0;
    }

    /**
     * Get item tax group
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    public function getItemTaxGroup($item)
    {
        if ($this->isProductCalculated($item)) {
            return array();
        }

        $id = $item->getId();
        $ratesData = $this->_getRates();

        $jurisdictionRates = isset($ratesData['items'][$id]['jurisdiction_rates'])
            ? $ratesData['items'][$id]['jurisdiction_rates']
            : array();

        $taxGroup = array();
        foreach ($jurisdictionRates as $jurisdiction => $rate) {
            $taxGroup[] = array(
                'rates'   => array(
                    array(
                        'code'     => $jurisdiction,
                        'title'    => $jurisdiction,
                        'percent'  => $rate,
                        'position' => 0,
                        'priority' => 0,
                        'rule_id'  => 0
                    )
                ),
                'percent' => $rate,
                'id'      => $jurisdiction
            );
        }

        return $taxGroup;
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
        $ratesData = $this->_getRates();
        $id = $item->getId();

        return isset($ratesData['gw_items'][$id]['amt']) ? $ratesData['gw_items'][$id]['amt'] : 0;
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

                return (float)$tax;
            } else {
                $ratesData = $this->_getRates();;
                $id = $item->getId();
                $tax = isset($ratesData['items'][$id]['amt']) ? $ratesData['items'][$id]['amt'] : 0;

                return (float)$tax;
            }
        }

        return 0;
    }

    /**
     * Get tax detail summary
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return array
     */
    public function getSummary($address)
    {
        return $this->_getService()->getSummary($address);
    }

    /**
     * Test to see if the product carries its own numbers or is calculated based on parent or children
     *
     * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item|mixed $item
     * @return bool
     */
    public function isProductCalculated($item)
    {
        return $this->_getService()->isProductCalculated($item);
    }

    /**
     * Check address
     *
     * @param array $params
     * @return $this
     * @throws \OnePica_AvaTax_Exception
     */
    protected function _checkAddress(array $params)
    {
        if (!isset($params[self::ADDRESS_PARAMETER])
            || !$params[self::ADDRESS_PARAMETER] instanceof Mage_Sales_Model_Quote_Address
        ) {
            throw new OnePica_AvaTax_Exception($this->_getHelper()->__('Address object is wrong.'));
        }

        return $this;
    }
}
