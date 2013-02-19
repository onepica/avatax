<?php
/**
 * CancelTaxRequest.class.php
 * @package Tax
 */


 /**
 * Data to pass to CancelTax indicating
 * the document that should be cancelled and the reason for the operation.
 * <p>
 * A document can be indicated solely by the DocId if it is known.
 * Otherwise the request must specify all of CompanyCode,
 * DocCode, and
 * DocType in order to uniquely identify the document.
 * </p>
 *
 * @package Tax
 * @see CancelTaxResult, DocumentType
 * @author tblanchard
 * Copyright (c) 2008, Avalara.  All rights reserved.
 *
 */

class CancelTaxRequest extends TaxRequest
{
    private $CancelCode;   //Unspecified or PostFailed or DocDeleted or DocVoided or AdjustmentCancelled

	public function __construct()
	{
		$this->DocType = DocumentType::$SalesInvoice;  // this is right Document
		$this->CancelCode = CancelCode::$Unspecified;
	}


    /**
     *   A code indicating the reason the document is getting canceled.
     *
     * @return string
     * @see CancelCode
     */

    public function getCancelCode() { return $this->CancelCode; }
	
	
    /**
     *   A code indicating the reason the document is getting canceled.
     *
     * @var string
     * @see CancelCode
     */

    public function setCancelCode($value) { CancelCode::Validate($value); $this->CancelCode = $value; return $this; }
	
}


 


?>