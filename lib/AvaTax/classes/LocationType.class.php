<?php
/**
 * SeverityLevel.class.php
 */

/**
 * Severity of the result {@link Message}.
 *
 * Defines the constants used to specify SeverityLevel in {@link Message}
 *
 * @author    Avalara
 * @copyright © 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Address
 */
 
class LocationType extends Enum
{
    public static $ShipFrom = 'ShipFrom';
    public static $ShipTo = 'ShipTo';
    //public static $PointOfSale = 'PointOfSale';		//Removed from Taxsvc2
    public static $PointOfOrderOrigin = 'PointOfOrderOrigin';
    public static $PointOfOrderAcceptance = 'PointOfOrderAcceptance';
 
	public static function Values()
	{
		return array(
			LocationType::$ShipFrom,
			LocationType::$ShipTo,
			LocationType::$PointOfOrderOrigin,
			LocationType::$PointOfOrderAcceptance
		);
	}
	
    // Unfortunate boiler plate due to polymorphism issues on static functions
    public static function Validate($value) { self::__Validate($value,self::Values(),__CLASS__); }
}