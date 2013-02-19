<?php
/**
 * AuditMessage.class.php
 *
 * @package Batch
 */
class AuditMessage {
  private $Message; // string

  public function setMessage($value){$this->Message=$value;} // string
  public function getMessage(){return $this->Message;} // string

}

?>
