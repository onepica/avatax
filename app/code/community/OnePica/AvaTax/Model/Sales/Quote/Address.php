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
 * The Sales Quote Address model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Sales_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    /**
     * Creates a hash key based on only address data for caching
     *
     * @return string
     */
    public function getCacheHashKey()
    {
        if (!$this->getData('cache_hash_key')) {
            $this->setData('cache_hash_key', hash('md4', $this->format('text')));
        }
        return $this->getData('cache_hash_key');
    }

    /**
     * Validates the address.  AvaTax validation is invoked if the this is a ship-to address.
     * Returns true on success and an array with an error on failure.
     *
     * @return true|array
     */
    public function validate()
    {
        if (!$this->_getConfigHelper()->fullStopOnError($this->getQuote()->getStoreId())) {
            return true;
        }

        $result = parent::validate();

        //if base validation fails, don't bother with additional validation
        if ($result !== true) {
            return $result;
        }

        //if ship-to address, do AvaTax validation
        $data = Mage::app()->getRequest()->getPost('billing', array());
        $useForShipping = isset($data['use_for_shipping']) ? (int)$data['use_for_shipping'] : 0;

        if ($this->getAddressType() == self::TYPE_SHIPPING
            || $this->getUseForShipping()/* <1.9 */ || $useForShipping/* >=1.9 */
        ) {
            return Mage::getModel('avatax/action_validator')->validate($this);
        }

        return $result;
    }

    /* BELOW ARE MAGE CORE PROPERTIES AND METHODS ADDED FOR OLDER VERSION COMPATABILITY */

    /**
     * Total amount
     *
     * @var array
     */
    protected $_totalAmounts = array();

    /**
     * Base total amount
     *
     * @var array
     */
    protected $_baseTotalAmounts = array();

    /**
     * Add amount total amount value
     *
     * @param   string $code
     * @param   float $amount
     * @return  Mage_Sales_Model_Quote_Address
     */
    public function addTotalAmount($code, $amount)
    {
        $amount = $this->getTotalAmount($code) + $amount;
        $this->setTotalAmount($code, $amount);
        return $this;
    }

    /**
     * Add amount total amount value in base store currency
     *
     * @param   string $code
     * @param   float $amount
     * @return  Mage_Sales_Model_Quote_Address
     */
    public function addBaseTotalAmount($code, $amount)
    {
        $amount = $this->getBaseTotalAmount($code) + $amount;
        $this->setBaseTotalAmount($code, $amount);
        return $this;
    }

    /**
     * Set total amount value
     *
     * @param   string $code
     * @param   float $amount
     * @return  Mage_Sales_Model_Quote_Address
     */
    public function setTotalAmount($code, $amount)
    {
        $this->_totalAmounts[$code] = $amount;
        if ($code != 'subtotal') {
            $code = $code . '_amount';
        }
        $this->setData($code, $amount);
        return $this;
    }

    /**
     * Set total amount value in base store currency
     *
     * @param   string $code
     * @param   float $amount
     * @return  Mage_Sales_Model_Quote_Address
     */
    public function setBaseTotalAmount($code, $amount)
    {
        $this->_baseTotalAmounts[$code] = $amount;
        if ($code != 'subtotal') {
            $code = $code . '_amount';
        }
        $this->setData('base_' . $code, $amount);
        return $this;
    }

    /**
     * Get total amount value by code
     *
     * @param   string $code
     * @return  float
     */
    public function getTotalAmount($code)
    {
        if (isset($this->_totalAmounts[$code])) {
            return $this->_totalAmounts[$code];
        }
        return 0;
    }

    /**
     * Get total amount value by code in base store curncy
     *
     * @param   string $code
     * @return  float
     */
    public function getBaseTotalAmount($code)
    {
        if (isset($this->_baseTotalAmounts[$code])) {
            return $this->_baseTotalAmounts[$code];
        }
        return 0;
    }

    /**
     * Get avatax config helper
     *
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
    }
}
