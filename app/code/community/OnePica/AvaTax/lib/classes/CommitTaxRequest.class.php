<?php
/**
 * CommitTaxRequest.class.php
 * @package Tax
 */

/**
 * Data to pass to {@link TaxServiceSoap#commitTax}.
 * <p>
 * A document can be indicated solely by the {@link CommitTaxRequest#DocId} if it is known.
 * Otherwise the request must specify all of {@link CommitTaxRequest#CompanyCode},
 * {@link CommitTaxRequest#DocCode}, and
 * {@link CommitTaxRequest#tDocType} in order to uniquely identify the document.
 * </p>
 *
 * @package Tax
 * @see CommitTaxResult
 * @author tblanchard
 * Copyright (c) 2007, Avalara.  All rights reserved.
 */
 
class CommitTaxRequest extends TaxRequest
{
	private $NewDocCode;  //string
	
	/**
	 * As on this version of SDK DocCode can be changed during commit using NewDocCode. 
	 *
	 * @return string
	 */
	public function getNewDocCode() { return $this->NewDocCode; }
	
	/**
	 * As on this version of SDK DocCode can be changed during commit using NewDocCode. 
	 *
	 * @param string $value
	 */
	public function setNewDocCode($value) { $this->NewDocCode = $value; }
	
	
	

}


?>