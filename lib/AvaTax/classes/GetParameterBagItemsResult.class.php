<?php
/**
 * GetParameterBagItemsResult.class.php		-	Changed for 15.6.0.0
 * 
 * @author    Avalara
 * @copyright © 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Tax
 */

class GetParameterBagItemsResult //extends BaseResult
{
    private $ParameterBags;		//array

	public function setParameterBags($value) { $this->ParameterBags = $value; return $this; }		//array		Changed for 15.6.0.0

	public function getParameterBags()
	{
		return is_array($this->ParameterBags) ? $this->ParameterBags : EnsureIsArray($this->ParameterBags->ParameterBag);
	}					//array		Changed for 15.6.0.0
}