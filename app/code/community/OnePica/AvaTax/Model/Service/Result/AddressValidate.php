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
 * The AvaTax Service Result Address Validate model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Result_AddressValidate
{
    /**
     * Has error
     *
     * @var bool
     */
    protected $_hasError = false;

    /**
     * Errors
     *
     * @var array
     */
    protected $_errors;

    /**
     * Resolution
     *
     * @var bool
     */
    protected $_resolution = false;

    /**
     * Address
     *
     * @var OnePica_AvaTax_Model_Service_Result_Address
     */
    protected $_address;

    /**
     * Is taxable
     *
     * @var bool
     */
    protected $_isTaxable = false;

    /**
     * Get Has Error
     *
     * @return bool
     */
    public function getHasError()
    {
        return $this->_hasError;
    }

    /**
     * Set Has Error
     *
     * @param bool $value
     * @return $this
     */
    public function setHasError($value)
    {
        $this->_hasError = $value;
        return $this;
    }

    /**
     * Get Errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Set Errors
     *
     * @param array $value
     * @return $this
     */
    public function setErrors($value)
    {
        $this->_errors = $value;
        return $this;
    }

    /**
     * Get Resolution
     *
     * @return bool
     */
    public function getResolution()
    {
        return $this->_resolution;
    }

    /**
     * Set Resolution
     *
     * @param bool $value
     * @return $this
     */
    public function setResolution($value)
    {
        $this->_resolution = $value;
        return $this;
    }

    /**
     * Get Address
     *
     * @return OnePica_AvaTax_Model_Service_Result_Address
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * Set Address
     *
     * @param OnePica_AvaTax_Model_Service_Result_Address $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->_address = $address;
        return $this;
    }

    /**
     * Is taxable
     *
     * @return bool
     */
    public function isTaxable()
    {
        return $this->_isTaxable;
    }

    /**
     * Set isTaxable
     *
     * @param bool $isTaxable
     * @return $this
     */
    public function setIsTaxable($isTaxable)
    {
        $this->_isTaxable = $isTaxable;

        return $this;
    }
}
