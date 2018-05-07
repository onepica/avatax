<?php
/**	New class for Changed for 15.6.0.0
 * AddressLocationType.class.php
 
 * @author    Avalara
 * @copyright © 2004 - 2016 Avalara, Inc.  All rights reserved.
 * @package   Address
 */
 
class AddressLocationType
{
    private $AddressCode;			//string
	private $LocationTypeCode;		//enum

    public function getAddressCode() { return $this->AddressCode; }
    public function getLocationTypeCode() { return $this->LocationTypeCode; }

    public function setAddressCode($value) { $this->AddressCode = $value; return $this; }
    public function setLocationTypeCode($value) { LocationType::Validate($value); $this->LocationTypeCode = $value; return $this; }
}