<?php
/**
 * TextCase.class.php
 * @package Address
 */

/**
 * The casing to apply to the valid address(es) returned in the validation result.
 *
 * @package Address
 * @author tblanchard
 * Copyright (c) 2007, Avalara.  All rights reserved.
 */

class TextCase extends Enum
{
	public static $Default 	= 'Default';
	public static $Upper 	= 'Upper';
	public static $Mixed 	= 'Mixed';
    
	public static function Values()
	{
		return array(
			TextCase::$Default,
			TextCase::$Upper,
			TextCase::$Mixed,
		);
	}

    // Unfortunate boiler plate due to polymorphism issues on static functions
    public static function Validate($value) { self::__Validate($value,self::Values(),__CLASS__); }
}

?>