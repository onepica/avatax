<?php
/**
 * BatchDeleteResponse.class.php
 */

/**
 *
 *
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Batch
 */
class BatchDeleteResponse {
  private $BatchDeleteResult; // DeleteResult

  public function setBatchDeleteResult($value){$this->BatchDeleteResult=$value;} // DeleteResult
  public function getBatchDeleteResult(){return $this->BatchDeleteResult;} // DeleteResult

}
