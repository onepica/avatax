<?php
/**
 * ServiceMode.class.php
 * @package Tax
 */
 
/**
 * Specifies the ServiceMode.
 *
 * @package Tax
 * @see GetTaxRequest, GetTaxHistoryRequest
 * @author swetal
 * Copyright (c) 2008, Avalara.  All rights reserved.
 * This is only supported by AvaLocal servers. It provides the ability to controls whether tax is calculated locally or remotely when using an AvaLocal server.
 * The default is Automatic which calculates locally unless remote is necessary for non-local addresses*/

class ServiceMode extends Enum
{
     /**
     * Automated handling by local and/or remote server.
     */
    public static $Automatic = "Automatic";


    /**
     * AvaLocal server only. Lines requiring remote will not be calculated.
     */
    public static $Local = "Local";

    /**
     * All lines are calculated by AvaTax remote server.
     */
    public static $Remote = "Remote";
    
    public static function Values()
	{
		return array(
			ServiceMode::$Automatic,
			ServiceMode::$Local,
			ServiceMode::$Remote			
		);
	}
	
    // Unfortunate boiler plate due to polymorphism issues on static functions
    public static function Validate($value) { self::__Validate($value,self::Values(),__CLASS__); }
}
?>