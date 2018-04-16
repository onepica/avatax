<?php
/**
 * GetParameterBagItemsResult.class.php		-	Changed for 15.6.0.0
 * 
 * @author    Avalara
 * @copyright © 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Tax
 */

class ParameterBag //extends BaseResult
{
    private $ParameterBagId;		//Integer
    private $Category;				//String
    private $Country;				//String
    private $State;					//String
    private $Name;					//String
    private $DataType;				//String
    private $Description;			//String
    	
    public function setParameterBagId($value) { $this->ParameterBagId = $value;}
    public function setCategory($value) { $this->Category = $value;}
    public function setCountry($value) { $this->Country = $value;}
    public function setState($value) { $this->State = $value;}
    public function setName($value) { $this->Name = $value;}
    public function setDataType($value) { $this->DataType = $value;}
    public function setDescription($value) { $this->Description = $value;}
	 
 	public function getParameterBagId() { return $this->ParameterBagId;}	
 	public function getCategory() { return $this->Category;}	
 	public function getCountry() { return $this->Country;}	
 	public function getState() { return $this->State;}	
 	public function getName() { return $this->Name;}	
 	public function getDataType() { return $this->DataType;}	
 	public function getDescription() { return $this->Description;}	
}