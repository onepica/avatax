<?php
/**
 * Enum.class.php
 * @package Base
 */

/**
 * Abstract class for enumerated types - provides validation.
 *
 * @package Base
 * @author tblanchard
 * Copyright (c) 2008, Avalara.  All rights reserved.
 */
 
class Enum
{
    // Basic implementation - check and throw
    protected static function __Validate($value,$values,$class=__CLASS__) 
    { 
		foreach($values as $valid)
		{
			if($value == $valid)
			{
				return true;
			}
		}
		
		throw new Exception('Invalid '.$class.' "'.$value.'" - must be one of "'.implode('"|"',$values).'"');
    }
}

?>