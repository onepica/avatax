<?php
/**
 * SeverityLevel.class.php
 *
 * @package Address
 */

/**
 * Severity of the result {@link Message}.
 *
 * Defines the constants used to specify SeverityLevel in {@link Message}
 *
 * @package Address
 * @author tblanchard
 * Copyright (c) 2007, Avalara.  All rights reserved.
 */
 
class SeverityLevel extends Enum
{
    public static $Success = 'Success';
    public static $Warning = 'Warning';
    public static $Error = 'Error';
    public static $Exception = 'Exception';
 
	
	public static function Values()
	{
		return array(
			SeverityLevel::$Success,
			SeverityLevel::$Warning,
			SeverityLevel::$Error,
			SeverityLevel::$Tax
		);
	}
	
    // Unfortunate boiler plate due to polymorphism issues on static functions
    public static function Validate($value) { self::__Validate($value,self::Values(),__CLASS__); }
}

?>