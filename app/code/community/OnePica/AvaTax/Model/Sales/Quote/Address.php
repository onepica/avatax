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
 * The Sales Quote Address model.
 */
class OnePica_AvaTax_Model_Sales_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    /**
     * Avatax address validator instance
     *
     * @var OnePica_AvaTax_Model_Avatax_Address
     */
    protected $_avataxValidator = null;

    /**
     * Validation results array (to avoid double valiadation of same address)
     *
     * @var array
     */
    static protected $_validationResult = array();

    /**
     * Avatax address validator accessor method
     *
     * @return OnePica_AvaTax_Model_Avatax_Address
     */
    public function getAvataxValidator() {
        return $this->_avataxValidator;
    }

    /**
     * Avatax address validator mutator method
     *
     * @return OnePica_AvaTax_Model_Avatax_Address
     * @return self
     */
    public function setAvataxValidator(OnePica_AvaTax_Model_Avatax_Address $object) {
        $this->_avataxValidator = $object;
        return $this;
    }

    /**
     * Creates a hash key based on only address data for caching
     *
     * @return string
     */
    public function getCacheHashKey() {
        if(!$this->getData('cache_hash_key')) {
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
    public function validate () {

        if (! Mage::helper('avatax')->fullStopOnError()) {
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

        if($this->getAddressType() == self::TYPE_SHIPPING || $this->getUseForShipping() /* <1.9 */ || $useForShipping /* >=1.9 */) {
            if (!isset(self::$_validationResult[$this->getAddressId()])) {
                if(!$this->getAvataxValidator()) {
                    $validator = Mage::getModel('avatax/avatax_address')->setAddress($this);
                    $this->setAvataxValidator($validator);
                }

                self::$_validationResult[$this->getAddressId()] = $this->getAvataxValidator()->validate();
            }

            return self::$_validationResult[$this->getAddressId()];
        }

        return $result;
    }


    /* BELOW ARE MAGE CORE PROPERTIES AND METHODS ADDED FOR OLDER VERSION COMPATABILITY */

    protected $_totalAmounts = array();
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
        $amount = $this->getTotalAmount($code)+$amount;
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
        $amount = $this->getBaseTotalAmount($code)+$amount;
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
            $code = $code.'_amount';
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
            $code = $code.'_amount';
        }
        $this->setData('base_'.$code, $amount);
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
            return  $this->_totalAmounts[$code];
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
            return  $this->_baseTotalAmounts[$code];
        }
        return 0;
    }
}
