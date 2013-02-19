<?php
/**
 * BatchSaveResult.class.php
 *
 * @package Batch
 */
class BatchSaveResult extends BaseResult {
  private $BatchId; // int
  private $EstimatedCompletion; // dateTime

  public function setBatchId($value){$this->BatchId=$value;} // int
  public function getBatchId(){return $this->BatchId;} // int

  public function setEstimatedCompletion($value){$this->EstimatedCompletion=$value;} // dateTime
  public function getEstimatedCompletion(){return $this->EstimatedCompletion;} // dateTime

}

?>
