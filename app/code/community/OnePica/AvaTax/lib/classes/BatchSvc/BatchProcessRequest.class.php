<?php
/**
 * BatchProcessRequest.class.php
 *
 * @package Batch
 */
class BatchProcessRequest //extends FilterRequest
{
  private $Filters; // string
  private $MaxCount; // int

  function __construct()
  {
  	$this->MaxCount=0;
  	
  }
  
  public function setFilters($value){$this->Filters=$value;} // string
  public function getFilters(){return $this->Filters;} // string

  public function setMaxCount($value){$this->MaxCount=$value;} // int
  public function getMaxCount(){return $this->MaxCount;} // int

}

?>
