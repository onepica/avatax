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
 * The AvaTax Service Result Address model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Result_Address
{
    /**
     * Line 1
     *
     * @var string
     */
    protected $_line1;

    /**
     * Line 2
     *
     * @var string
     */
    protected $_line2;

    /**
     * City
     *
     * @var string
     */
    protected $_city;

    /**
     * State
     *
     * @var string
     */
    protected $_state;

    /**
     * Country
     *
     * @var string
     */
    protected $_country;

    /**
     * Postal Code
     *
     * @var string
     */
    protected $_postalCode;

    /**
     * Get line 1
     *
     * @return string|null
     */
    public function getLine1()
    {
        return $this->_line1;
    }

    /**
     * Set line 1
     *
     * @param string $value
     * @return $this
     */
    public function setLine1($value)
    {
        $this->_line1 = $value;
        return $this;
    }

    /**
     * Get line 2
     *
     * @return string|null
     */
    public function getLine2()
    {
        return $this->_line2;
    }

    /**
     * Set line 2
     *
     * @param string $value
     * @return $this
     */
    public function setLine2($value)
    {
        $this->_line2 = $value;
        return $this;
    }

    /**
     * Get City
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->_city;
    }

    /**
     * Set City
     *
     * @param string $value
     * @return $this
     */
    public function setCity($value)
    {
        $this->_city = $value;
        return $this;
    }

    /**
     * Get State
     *
     * @return string|null
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     * Set State
     *
     * @param string $value
     * @return $this
     */
    public function setState($value)
    {
        $this->_state = $value;
        return $this;
    }

    /**
     * Get Country
     *
     * @return string|null
     */
    public function getCountry()
    {
        return $this->_country;
    }

    /**
     * Set Country
     *
     * @param string $value
     * @return $this
     */
    public function setCountry($value)
    {
        $this->_country = $value;
        return $this;
    }

    /**
     * Get Postal Code
     *
     * @return string|null
     */
    public function getPostalCode()
    {
        return $this->_postalCode;
    }

    /**
     * Set Postal Code
     *
     * @param string $value
     * @return $this
     */
    public function setPostalCode($value)
    {
        $this->_postalCode = $value;
        return $this;
    }
}
