<?php
/**
 * TaxType.class.php
 * @package Tax
 */

/**
 * The Type of the tax.
 *
 * @package Tax
 * @author tblanchard
 * Copyright (c) 2008, Avalara.  All rights reserved.
 */

class TaxType// extends Enum
{
	public static $Sales	= 'Sales';
	public static $Use		= 'Use';
	public static $ConsumerUse	= 'ConsumerUse';
	/*
    public static function Values()
	{
		return array(
			$TaxType::$Sales,
			$TaxType::$Use,
			$TaxType::$ConsumerUse
		);
	}
	
    // Unfortunate boiler plate due to polymorphism issues on static functions
    public static function Validate($value) { self::__Validate($value,self::Values(),__CLASS__); }
	
	*/
	
}

	

?>