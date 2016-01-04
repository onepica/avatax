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
 * The AvaTax Service Result Creditmemo model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Result_Creditmemo
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
     * Document Code
     *
     * @var string
     */
    protected $_documentCode;

    /**
     * Total tax
     *
     * @var float
     */
    protected $_totalTax;

    /**
     * Get has error
     *
     * @return bool
     */
    public function getHasError()
    {
        return $this->_hasError;
    }

    /**
     * Set has error
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
     * Get errors
     *
     * @return array|null
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Set errors
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
     * Get Document Code
     *
     * @return string|null
     */
    public function getDocumentCode()
    {
        return $this->_documentCode;
    }

    /**
     * Set Document Code
     *
     * @param string $value
     * @return $this
     */
    public function setDocumentCode($value)
    {
        $this->_documentCode = $value;
        return $this;
    }

    /**
     * Get Total Tax
     *
     * @return float|null
     */
    public function getTotalTax()
    {
        return $this->_totalTax;
    }

    /**
     * Set Total Tax
     *
     * @param float $value
     * @return $this
     */
    public function setTotalTax($value)
    {
        $this->_totalTax = $value;
        return $this;
    }
}
