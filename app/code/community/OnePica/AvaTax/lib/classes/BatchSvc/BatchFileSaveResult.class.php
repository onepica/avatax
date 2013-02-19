<?php
/**
 * BatchFileSaveResult.class.php
 *
 * @package Batch
 */
class BatchFileSaveResult extends BaseResult {
  private $BatchFileId; // int

  public function setBatchFileId($value){$this->BatchFileId=$value;} // int
  public function getBatchFileId(){return $this->BatchFileId;} // int

}

?>
