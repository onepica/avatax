<?php
/**
 * BatchFetch.class.php
 */

/**
 * 
 *
 * @author    Avalara
 * @copyright © 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Batch
 */
class BatchFetch {
  private $FetchRequest; // FetchRequest

  public function setFetchRequest($value){$this->FetchRequest=$value;} // FetchRequest
  public function getFetchRequest(){return $this->FetchRequest;} // FetchRequest

}