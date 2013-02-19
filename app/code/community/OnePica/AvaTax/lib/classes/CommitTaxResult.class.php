<?php
/**
 * CommitTaxResult.class.php
 * @package Tax
 */

/**
 * Result data returned from {@link TaxServiceSoap#commitTax}.
 *
 * @package Tax
 * @see CommitTaxRequest
 * @author tblanchard
 * Copyright (c) 2008, Avalara.  All rights reserved.
 */

class CommitTaxResult //extends BaseResult
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