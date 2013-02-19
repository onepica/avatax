<?php
/**
 * BoundaryLevel.class.php
 * @package Tax
 */

/**
 * BoundaryLevel.class.php
 *
 * Jurisdiction boundary precision level found for address;
 * This depends on the accuracy of the address as well as the
 * precision level of the state provided jurisdiction boundaries.
 * @package Tax
 * @see TaxLine
 * @author tblanchard
 * Copyright (c) 2008, Avalara.  All rights reserved.
*/

class BoundaryLevel extends Enum
{

	/**
	 * Street address precision
	 *
	 * @var unknown_type
	 */
	public static $Address		= 'Address'; //enum 
	
	/**
	 *5-digit zip precision
	 *
	 * @var unknown_type
	 */
	public static $Zip9			= 'Zip9'; //enum

	/**
	 *9-digit zip precision
	 *
	 * @var unknown_type
	 */
	public static $Zip5			= 'Zip5'; //enum 
	

    
	public static function Values()
	{
		return array(
			BoundaryLevel::$Address,
			BoundaryLevel::$Zip9,
			BoundaryLevel::$Zip5
		);
	}

    
    // Unfortunate boiler plate due to polymorphism issues on static functions
    public static function Validate($value) { self::__Validate($value,self::Values(),__CLASS__); }
}
	

?>