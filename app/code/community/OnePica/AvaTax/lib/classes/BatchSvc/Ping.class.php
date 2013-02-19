<?php
/**
 * Ping.class.php
 *
 * @package Batch
 */
class Ping {
  private $Message; // string

  public function setMessage($value){$this->Message=$value;} // string
  public function getMessage(){return $this->Message;} // string

}

?>
