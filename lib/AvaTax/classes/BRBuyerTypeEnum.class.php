<?php
/**
 * BRBuyerTypeEnum.class.php
 */
 
 /**
 * 
 * 
 *
 * @author    Avalara
 * @copyright © 2004 - 2016 Avalara, Inc.  All rights reserved.
 * @package   Tax
 */


class BRBuyerTypeEnum extends Enum
{
	 /* @var BRBuyerTypeEnum
	 */
	public static $IND		= 'IND';	
    public static $BUS		= 'BUS';
    public static $GOV		= 'GOV';
    
	public static function Values()
	{
		return array(
			BRBuyerTypeEnum::$IND,
			BRBuyerTypeEnum::$BUS,
			BRBuyerTypeEnum::$GOV
		);
	}
	// Unfortunate boiler plate due to polymorphism issues on static functions
    public static function Validate($value) { self::__Validate($value,self::Values(),__CLASS__); }
}