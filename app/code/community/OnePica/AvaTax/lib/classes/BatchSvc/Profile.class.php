<?php
/**
 * Profile.class.php
 *
 * @package Batch
 */
class Profile {
  private $Name; // string
  private $Client; // string
  private $Adapter; // string
  private $Machine; // string

  public function setName($value){$this->Name=$value;} // string
  public function getName(){return $this->Name;} // string

  public function setClient($value){$this->Client=$value;} // string
  public function getClient(){return $this->Client;} // string

  public function setAdapter($value){$this->Adapter=$value;} // string
  public function getAdapter(){return $this->Adapter;} // string

  public function setMachine($value){$this->Machine=$value;} // string
  public function getMachine(){return $this->Machine;} // string

}

?>
