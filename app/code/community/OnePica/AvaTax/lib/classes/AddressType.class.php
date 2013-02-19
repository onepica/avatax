<?php
/**
 * AddressType.class.php
 * @package Address
 */

/**
 * The type of the address(es) returned in the validation result.
 *
 * @package Address
 * @author tblanchard
 * Copyright (c) 2007, Avalara.  All rights reserved.
 */

class AddressType extends Enum
{
	public static $FirmOrCompany 	= 'F';
	public static $GeneralDelivery 	= 'G';
	public static $HighRise         = 'H';
    public static $POBox            = 'P';
    public static $RuralRoute       = 'R';
    public static $StreetOrResidential = 'S';
    
	public static function Values()
	{
		return array(
            'FirmOrCompany'         => AddressType::$FirmOrCompany,
            'GeneralDelivery'       => AddressType::$GeneralDelivery,
            'HighRise'              => AddressType::$HighRise,
            'POBox'                 => AddressType::$POBox,
            'RuralRoute'            => AddressType::$RuralRoute,
            'StreetOrResidential'   => AddressType::$StreetOrResidential
		);
	}

    // Unfortunate boiler plate due to polymorphism issues on static functions
    public static function Validate($value) { self::__Validate($value,self::Values(),__CLASS__); }
}

?>
