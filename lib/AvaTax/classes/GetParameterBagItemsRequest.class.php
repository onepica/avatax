<?php
/**
 * GetParameterBagItemsRequest.class.php	-	Added for 15.6.0.0
 */

/**
 * @author    Avalara
 * @copyright © 2004 - 2016 Avalara, Inc.  All rights reserved.
 * @package   Tax
 */
   
class GetParameterBagItemsRequest 
{
	private $Category;		//string
	private $Country;		//string
	private $State;			//string

		
    /**     
     *
     * @param string $value
     */
	public function setCategory($value) { $this->Category = $value; }

	/**
	 * @param string $value	 
	 */		
    public function setCountry($value) { $this->Country = $value;}
    
	/**
	 *
	 * @param string $value
	 */
    public function setState($value) { $this->State = $value;}
	 
 	public function getCategory() { return $this->Category;}	

    public function getCountry() { return $this->Country;}	

    public function getState() { return $this->State;}   	
}