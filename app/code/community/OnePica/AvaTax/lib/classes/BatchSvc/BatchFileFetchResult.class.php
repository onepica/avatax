<?php
/**
 * BatchFileFetchResult.class.php
 *
 * @package Batch
 */
class BatchFileFetchResult extends BaseResult {
  private $BatchFiles; // ArrayOfBatchFile
  private $RecordCount; // int

  public function setBatchFiles($value){$this->BatchFiles=$value;} // ArrayOfBatchFile
  public function getBatchFiles(){return $this->BatchFiles;} // ArrayOfBatchFile

  public function setRecordCount($value){$this->RecordCount=$value;} // int
  public function getRecordCount(){return $this->RecordCount;} // int

}

?>
