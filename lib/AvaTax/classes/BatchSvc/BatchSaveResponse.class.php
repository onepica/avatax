<?php
/**
 * BatchSaveResponse.class.php
 */

/**
 *
 *
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Batch
 */
class BatchSaveResponse {
  private $BatchSaveResult; // BatchSaveResult

  public function setBatchSaveResult($value){$this->BatchSaveResult=$value;} // BatchSaveResult
  public function getBatchSaveResult(){return $this->BatchSaveResult;} // BatchSaveResult

}
