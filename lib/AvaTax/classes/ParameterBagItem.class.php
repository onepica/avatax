<?php
/**	New class for Changed for 15.6.0.0
 * ParameterBagItem.class.php
 
 * @author    Avalara
 * @copyright © 2004 - 2016 Avalara, Inc.  All rights reserved.
 * @package   Address
 */
 
class ParameterBagItem
{
    private $Name;
	private $Value;
	private $UOMCode;

    public function getName() { return $this->Name; }
    public function getValue() { return $this->Value; }
    public function getUOMCode() { return $this->UOMCode; }

    public function setName($value) { $this->Name = $value; return $this; }
    public function setValue($value) { $this->Value = $value; return $this; }
    public function setUOMCode($value) { $this->UOMCode = $value; return $this; }
}