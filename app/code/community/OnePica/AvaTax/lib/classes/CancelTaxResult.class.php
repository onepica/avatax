<?php
/**
 * CancelTaxResult.class.php
 *
 * @package Tax
 */

/**
 * CancelTaxResult.class.php
 *
 **
 * Result data returned from {@link TaxSvcSoap#cancelTax}
 * @see CancelTaxRequest
 * @author tblanchard
 * @package Tax
 * Copyright (c) 2008, Avalara.  All rights reserved.
 */

class CancelTaxResult // extends BaseResult
{
    

// BaseResult innards - workaround a bug in SoapClient

/**
 * @var string
 */
    private $TransactionId;
/**
 * @var string must be one of the values defined in {@link SeverityLevel}.
 */
    private $ResultCode = 'Success';
/**
 * @var array of Message.
 */
    private $Messages = array();

/**
 * Accessor
 * @return string
 */
    public function getTransactionId() { return $this->TransactionId; }
/**
 * Accessor
 * @return string
 */
    public function getResultCode() { return $this->ResultCode; }
/**
 * Accessor
 * @return array
 */
    public function getMessages() { return EnsureIsArray($this->Messages->Message); }



}

?>