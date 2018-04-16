<?php
/**
 * GetParameterBagItemsResult.class.php		-	Added for 15.6.0.0
 * 
 * @author    Avalara
 * @copyright © 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Tax
 */

class GetAllParameterBagItemsResult //extends BaseResult
{
    private $ParameterBags;		//array

	public function setParameterBags($value) { $this->ParameterBags = $value; return $this; }		//array	

	public function getParameterBags()
	{
		return is_array($this->ParameterBags) ? $this->ParameterBags : EnsureIsArray($this->ParameterBags->ParameterBag);
	}					//array
}