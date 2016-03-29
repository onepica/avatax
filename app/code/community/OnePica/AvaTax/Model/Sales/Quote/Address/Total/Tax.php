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
 * Tax totals calculation model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Sales_Quote_Address_Total_Tax extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Item tax groups
     *
     * @var array
     */
    protected $_itemTaxGroups = array();

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->setCode('tax');
    }

    /**
     * Collect tax totals for quote address. If quote address doesn't have a
     * postal code or postal code is "-" (OneStepCheckout), no tax is requested
     * from Avatax. When selling to a country that doesn't require postal code
     * this could be a problem, but Avatax doesn't support those locations yet.
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_setAddress($address);
        parent::collect($address);

        if (!$address->getPostcode()
            || ($address->getPostcode() && $address->getPostcode() == '-')
        ) {
            return $this;
        }

        $store = $address->getQuote()->getStore();

        $this->_resetItemsValues($address);

        //Added check for calculating tax for regions filtered in the admin
        if (!$this->_isAddressActionable($address)
            || !$address->hasItems()
            || $this->_isFilteredRequest($store)
        ) {
            return $this;
        }

        $this->_resetAddressValues($address);

        $calculator = $this->_getCalculator($address);

        $this->_itemTaxGroups = array();
        $this->_applyItemTax($address, $calculator, $store);
        $this->_applyShippingTax($address, $store, $calculator);
        $this->_applyGwTax($address, $store, $calculator);
        $this->_setTaxForItems($address, $this->_itemTaxGroups);
        $summary = $calculator->getSummary($address);
        $this->_saveAppliedTax($address, $summary);

        return $this;
    }

    /**
     * Save applied tax
     *
     * @param Mage_Sales_Model_Quote_Address $address
     */
    protected function _saveAppliedTax($address, $summary)
    {
        $fullInfo = array();
        $store = $address->getQuote()->getStore();

        foreach ($summary as $key => $row) {
            $id = $row['name'];
            $fullInfo[$id] = array(
                'rates'       => array(
                    array(
                        'code'     => $row['name'],
                        'title'    => $row['name'],
                        'percent'  => $row['rate'],
                        'position' => $key,
                        'priority' => $key,
                        'rule_id'  => 0
                    )
                ),
                'percent'     => $row['rate'],
                'id'          => $id,
                'process'     => 0,
                'amount'      => $store->convertPrice($row['amt']),
                'base_amount' => $row['amt']
            );
        }

        $address->setAppliedTaxes($fullInfo);
    }

    /**
     * Check if address actionable to calculate tax
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    protected function _isAddressActionable($address)
    {
        /** @var OnePica_AvaTax_Helper_Data $helper */
        $helper = Mage::helper('avatax/address');
        $storeId = $address->getQuote()->getStoreId();

        return $helper->isAddressActionable(
            $address,
            $storeId,
            OnePica_AvaTax_Model_Service_Abstract_Config::REGIONFILTER_TAX
        );
    }

    /**
     * Set tax for items
     *
     * @param \Mage_Sales_Model_Quote_Address $address
     * @param array                           $itemTaxGroups
     * @return $this
     */
    protected function _setTaxForItems(Mage_Sales_Model_Quote_Address $address, $itemTaxGroups)
    {
        if ($address->getQuote()->getTaxesForItems()) {
            $itemTaxGroups += $address->getQuote()->getTaxesForItems();
        }
        $address->getQuote()->setTaxesForItems($itemTaxGroups);

        return $this;
    }

    /**
     * Add tax totals information to address object
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $config = Mage::getSingleton('tax/config');
        $quote = $address->getQuote();
        $store = $quote->getStore();
        $amount = $address->getTaxAmount();

        if (($amount != 0) || (Mage::helper('tax')->displayZeroTax($store))) {
            $address->addTotal(
                array(
                    'code'      => $this->getCode(),
                    'title'     => Mage::helper('tax')->__('Tax'),
                    'full_info' => $address->getAppliedTaxes(),
                    'value'     => $amount,
                    'area'      => null
                )
            );
        }

        /**
         * Modify subtotal
         */
        if (method_exists($config, "displayCartSubtotalBoth") && method_exists($config, "displayCartSubtotalInclTax")
            && ($config->displayCartSubtotalBoth($store) || $config->displayCartSubtotalInclTax($store))
        ) {
            if ($address->getSubtotalInclTax() > 0) {
                $subtotalInclTax = $address->getSubtotalInclTax();
            } else {
                $subtotalInclTax = $address->getSubtotal()
                                   + $address->getTaxAmount()
                                   - $address->getShippingTaxAmount();
            }

            $address->addTotal(
                array(
                    'code'           => 'subtotal',
                    'title'          => Mage::helper('sales')->__('Subtotal'),
                    'value'          => $subtotalInclTax,
                    'value_incl_tax' => $subtotalInclTax,
                    'value_excl_tax' => $address->getSubtotal(),
                )
            );
        }

        return $this;
    }

    /**
     * Get calculator model
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return \OnePica_AvaTax_Model_Action_Calculator
     */
    protected function _getCalculator(Mage_Sales_Model_Quote_Address $address)
    {
        return Mage::getModel(
            'avatax/action_calculator',
            array(OnePica_AvaTax_Model_Action_Calculator::ADDRESS_PARAMETER => $address)
        );
    }

    /**
     * Apply shipping tax
     *
     * @param \Mage_Sales_Model_Quote_Address        $address
     * @param Mage_Core_Model_Store|int              $store
     * @param OnePica_AvaTax_Model_Action_Calculator $calculator
     * @return $this
     */
    protected function _applyShippingTax(Mage_Sales_Model_Quote_Address $address, $store, $calculator)
    {
        if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING
            || $address->getUseForShipping()
            || $this->_isBillingUseForShipping()
        ) {
            $shippingItem = new Varien_Object();
            $shippingItem->setId(Mage::helper('avatax/config')->getShippingSku($store->getId()));
            $shippingItem->setProductId(Mage::helper('avatax/config')->getShippingSku($store->getId()));
            $shippingItem->setAddress($address);
            $baseShippingTax = $calculator->getItemTax($shippingItem);
            $shippingTax = $store->convertPrice($baseShippingTax);

            $baseShippingAmt = $address->getBaseTotalAmount('shipping');
            $shippingAmt = $address->getTotalAmount('shipping');

            $baseShippingInclTax = $baseShippingAmt + $baseShippingTax;
            $shippingInclTax = $shippingAmt + $shippingTax;

            if ($this->_getTaxDataHelper()->priceIncludesTax($store)) {
                $baseShippingInclTax = $baseShippingAmt;
                $shippingInclTax = $shippingAmt;

                $baseShippingAmt -= $baseShippingTax;
                $shippingAmt = $store->convertPrice($baseShippingAmt);

                $address->setTotalAmount('shipping', $shippingAmt);
                $address->setBaseTotalAmount('shipping', $baseShippingAmt);
            }

            $address->setShippingTaxAmount($shippingTax);
            $address->setBaseShippingTaxAmount($baseShippingTax);

            $address->setShippingInclTax($shippingInclTax);
            $address->setBaseShippingInclTax($baseShippingInclTax);

            $address->setShippingTaxable($shippingTax ? $shippingAmt : 0);
            $address->setBaseShippingTaxable($baseShippingTax ? $baseShippingAmt : 0);

            $this->_addAmount($shippingTax);
            $this->_addBaseAmount($baseShippingTax);
        }

        return $this;
    }

    /**
     * Apply gift wrapping tax
     *
     * @param Mage_Sales_Model_Quote_Address         $address
     * @param Mage_Core_Model_Store|int              $store
     * @param OnePica_AvaTax_Model_Action_Calculator $calculator
     * @return $this
     */
    protected function _applyGwTax(Mage_Sales_Model_Quote_Address $address, $store, $calculator)
    {
        if ($address->getGwPrice() > 0) {
            $gwOrderItem = new Varien_Object();
            $gwOrderItem->setId(Mage::helper('avatax/config')->getGwOrderSku($store->getId()));
            $gwOrderItem->setProductId(Mage::helper('avatax/config')->getGwOrderSku($store->getId()));
            $gwOrderItem->setAddress($address);
            $baseGwOrderTax = $calculator->getItemTax($gwOrderItem);
            $gwOrderTax = $store->convertPrice($baseGwOrderTax);

            $address->setGwBaseTaxAmount($baseGwOrderTax);
            $address->setGwTaxAmount($gwOrderTax);

            if ($this->_getTaxDataHelper()->priceIncludesTax($store)) {
                $gwBasePriceAmount = $address->getGwBasePrice() - $baseGwOrderTax;
                $gwPriceAmount = $store->convertPrice($gwBasePriceAmount);
                $address->setGwBasePrice($gwBasePriceAmount);
                $address->setGwPrice($gwPriceAmount);

                $address->setGrandTotal($address->getGrandTotal() - $gwOrderTax);
                $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseGwOrderTax);
            }

            $this->_addAmount($gwOrderTax);
            $this->_addBaseAmount($baseGwOrderTax);
        }

        if ($address->getGwAddPrintedCard()) {
            $gwPrintedCardItem = new Varien_Object();
            $gwPrintedCardItem->setId(Mage::helper('avatax/config')->getGwPrintedCardSku($store->getId()));
            $gwPrintedCardItem->setProductId(Mage::helper('avatax/config')->getGwPrintedCardSku($store->getId()));
            $gwPrintedCardItem->setAddress($address);

            $baseGwPrintedCardTax = $calculator->getItemTax($gwPrintedCardItem);
            $gwPrintedCardTax = $store->convertPrice($baseGwPrintedCardTax);
            $address->setGwPrintedCardBaseTaxAmount($baseGwPrintedCardTax);
            $address->setGwPrintedCardTaxAmount($gwPrintedCardTax);

            if ($this->_getTaxDataHelper()->priceIncludesTax($store)) {
                $baseGwPrintedCardAmount = $address->getGwCardBasePrice() - $baseGwPrintedCardTax;
                $gwPrintedCardAmount = $store->convertPrice($baseGwPrintedCardAmount);
                $address->setGwCardPrice($gwPrintedCardAmount);
                $address->setGwCardBasePrice($baseGwPrintedCardAmount);

                $address->setGrandTotal($address->getGrandTotal() - $gwPrintedCardTax);
                $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseGwPrintedCardTax);
            }

            $this->_addAmount($gwPrintedCardTax);
            $this->_addBaseAmount($baseGwPrintedCardTax);
        }

        return $this;
    }

    /**
     * Reset items values
     *
     * @param \Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    protected function _resetItemsValues(Mage_Sales_Model_Quote_Address $address)
    {
        /* @var $item Mage_Sales_Model_Quote_Item */
        foreach ($address->getAllItems() as $item) {
            $item->setTaxAmount(0);
            $item->setBaseTaxAmount(0);
            $item->setTaxPercent(0);

            $item->setGwBaseTaxAmount(0);
            $item->setGwTaxAmount(0);
        }

        return $this;
    }
    /**
     * Reset address values
     *
     * @param \Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    protected function _resetAddressValues(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setTotalAmount($this->getCode(), 0);
        $address->setBaseTotalAmount($this->getCode(), 0);

        $address->setTaxAmount(0);
        $address->setBaseTaxAmount(0);
        $address->setShippingTaxAmount(0);
        $address->setBaseShippingTaxAmount(0);

        $address->setSubtotal(0);
        $address->setSubtotalInclTax(0);
        $address->setBaseSubtotalInclTax(0);
        $address->setTotalAmount('subtotal', 0);
        $address->setBaseTotalAmount('subtotal', 0);

        $address->setGwItemsTaxAmount(0);
        $address->setGwItemsBaseTaxAmount(0);
        $address->setGwBaseTaxAmount(0);
        $address->setGwTaxAmount(0);
        $address->setGwCardBaseTaxAmount(0);
        $address->setGwCardTaxAmount(0);

        return $this;
    }

    /**
     * Apply item tax
     *
     * @param Mage_Sales_Model_Quote_Address         $address
     * @param Mage_Core_Model_Store|int              $store
     * @param OnePica_AvaTax_Model_Action_Calculator $calculator
     * @return $this
     */
    protected function _applyItemTax(Mage_Sales_Model_Quote_Address $address, $calculator, $store)
    {
        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($address->getAllItems() as $item) {
            $item->setAddress($address);
            $baseAmount = $calculator->getItemTax($item);

            $giftBaseTaxTotalAmount = $calculator->getItemGiftTax($item);
            $this->_itemTaxGroups[$item->getId()] = $calculator->getItemTaxGroup($item);
            $giftTaxTotalAmount = $store->convertPrice($giftBaseTaxTotalAmount);
            $giftBaseTaxAmount = $this->_getDataHelper()->roundUp($giftBaseTaxTotalAmount / $item->getTotalQty(), 4);
            $giftTaxAmount = $store->convertPrice($giftBaseTaxAmount);

            $amount = $store->convertPrice($baseAmount);
            $percent = $calculator->getItemRate($item);

            $item->setTaxAmount($amount);
            $item->setBaseTaxAmount($baseAmount);
            $item->setTaxPercent($percent);

            $item->setGwBaseTaxAmount($giftBaseTaxAmount);
            $item->setGwTaxAmount($giftTaxAmount);

            $address->setGwItemsTaxAmount($address->getGwItemsTaxAmount() + $giftTaxTotalAmount);
            $address->setGwItemsBaseTaxAmount($address->getGwItemsBaseTaxAmount() + $giftBaseTaxTotalAmount);

            if ($this->_getTaxDataHelper()->priceIncludesTax($store)) {
                $basePriceIncTax = $item->getBasePrice();
                $priceIncTax = $item->getCalculationPrice();

                $baseRowTotalIncTax = $item->getBaseRowTotal();
                $rowTotalIncTax = $item->getRowTotal();

                $item->setPrice($item->getPrice() - ($baseAmount / $item->getTotalQty()));
                $item->setBasePrice($item->getBasePrice() - ($baseAmount / $item->getTotalQty()));

                $item->setBaseRowTax($baseAmount);
                $item->setRowTax($amount);

                $this->_calcItemRowTotal($item);

                $address->setGwItemsBasePrice($address->getGwItemsBasePrice() - $giftBaseTaxTotalAmount);
                $address->setGwItemsPrice($address->getGwItemsPrice() - $giftTaxTotalAmount);

                $item->setGwBasePrice($item->getGwBasePrice() - $giftBaseTaxAmount);
                $item->setGwPrice($item->getGwPrice() - $giftTaxAmount);

                $address->setBaseGrandTotal($address->getBaseGrandTotal() - $giftBaseTaxTotalAmount);
                $address->setGrandTotal($address->getGrandTotal() - $giftTaxTotalAmount);

                $item->setBasePriceInclTax($basePriceIncTax);
                $item->setPriceInclTax($priceIncTax);

                $item->setBaseRowTotalInclTax($baseRowTotalIncTax);
                $item->setRowTotalInclTax($rowTotalIncTax);
            } else {
                $item->setBasePriceInclTax($item->getBasePrice() + ($baseAmount / $item->getTotalQty()));
                $item->setPriceInclTax($item->getCalculationPrice() + ($amount / $item->getTotalQty()));

                $item->setBaseRowTotalInclTax($item->getBaseRowTotal() + $baseAmount);
                $item->setRowTotalInclTax($item->getRowTotal() + $amount);
            }

            if (!$calculator->isProductCalculated($item)) {
                $this->_addAmount($amount);
                $this->_addBaseAmount($baseAmount);
            }

            $this->_addAmount($giftTaxTotalAmount);
            $this->_addBaseAmount($giftBaseTaxTotalAmount);

            if (!$item->getParentItem()) {
                $this->_addSubtotalAmount($address, $item);
            }
        }

        return $this;
    }

    /**
     * Add row total item amount to subtotal
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   Mage_Sales_Model_Quote_Item    $item
     * @return  $this
     */
    protected function _addSubtotalAmount(Mage_Sales_Model_Quote_Address $address, $item)
    {
        if ($this->_getTaxDataHelper()->priceIncludesTax($item->getStoreId())) {
            $subTotal = $item->getRowTotalInclTax() - $item->getRowTax();
            $baseSubTotal = $item->getBaseRowTotalInclTax() - $item->getBaseRowTax();
            $address->setTotalAmount('subtotal', $address->getTotalAmount('subtotal') + $subTotal);
            $address->setBaseTotalAmount('subtotal', $address->getBaseTotalAmount('subtotal') + $baseSubTotal);
        } else {
            $address->setTotalAmount('subtotal', $address->getTotalAmount('subtotal') + $item->getRowTotal());
            $address->setBaseTotalAmount('subtotal',
                $address->getBaseTotalAmount('subtotal') + $item->getBaseRowTotal()
            );
        }

        $address->setSubtotalInclTax($address->getSubtotalInclTax() + $item->getRowTotalInclTax());
        $address->setBaseSubtotalInclTax($address->getBaseSubtotalInclTax() + $item->getBaseRowTotalInclTax());

        return $this;
    }

    /**
     * Calculate item row total
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return $this
     * @see Mage_Sales_Model_Quote_Item::calcRowTotal()
     */
    protected function _calcItemRowTotal($item)
    {
        $qty = $item->getTotalQty();
        $total = $this->_getDataHelper()->roundUp($item->getCalculationPriceOriginal(), 4) * $qty;
        $baseTotal = $this->_getDataHelper()->roundUp($item->getBaseCalculationPriceOriginal(), 4) * $qty;
        $item->setRowTotal($this->_getDataHelper()->roundUp($total, 4));
        $item->setBaseRowTotal($this->_getDataHelper()->roundUp($baseTotal, 4));

        return $this;
    }

    /**
     * Get tax data helper
     *
     * @return Mage_Tax_Helper_Data
     */
    protected function _getTaxDataHelper()
    {
        return Mage::helper('tax');
    }

    /**
     * Get data helper
     *
     * @return \OnePica_AvaTax_Helper_Data
     */
    protected function _getDataHelper()
    {
        return Mage::helper('avatax');
    }

    /**
     * Is billing address use for shipping
     *
     * @return bool
     */
    protected function _isBillingUseForShipping()
    {
        $data = Mage::app()->getRequest()->getPost('billing', array());

        return isset($data['use_for_shipping']) ? (bool)$data['use_for_shipping'] : false;
    }

    /* BELOW ARE MAGE CORE PROPERTIES AND METHODS ADDED FOR OLDER VERSION COMPATABILITY */

    /**
     * Total Code name
     *
     * @var string
     */
    protected $_code;

    /**
     * Quote address
     *
     * @var Mage_Sales_Model_Quote_Address|null
     */
    protected $_address = null;

    /**
     * Add total model amount value to address
     *
     * @param float $amount
     * @return Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _addAmount($amount)
    {
        $this->_getAddress()->addTotalAmount($this->getCode(), $amount);

        return $this;
    }

    /**
     * Add total model base amount value to address
     *
     * @param float $baseAmount
     * @return Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _addBaseAmount($baseAmount)
    {
        $this->_getAddress()->addBaseTotalAmount($this->getCode(), $baseAmount);

        return $this;
    }

    /**
     * Set address which can be used inside totals calculation
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _setAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;

        return $this;
    }

    /**
     * Get quote address object
     *
     * @return Mage_Sales_Model_Quote_Address
     * @throws OnePica_AvaTax_Exception
     */
    protected function _getAddress()
    {
        if ($this->_address === null) {
            throw new OnePica_AvaTax_Exception(Mage::helper('sales')->__('Address model is not defined'));
        }

        return $this->_address;
    }

    /**
     * Checks if is filtered request
     *
     * @param Mage_Core_Model_Store|int $store
     * @return bool
     */
    protected function _isFilteredRequest($store)
    {
        if ($this->_getRequestFilterHelper()->isRequestFiltered($store)) {
            return true;
        }

        return false;
    }

    /**
     * Get request filter helper
     *
     * @return OnePica_AvaTax_Helper_RequestFilter
     */
    protected function _getRequestFilterHelper()
    {
        return Mage::helper('avatax/requestFilter');
    }
}
