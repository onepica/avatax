<?php
/**
 * OnePica_AvaTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use Avalara\AvaTaxRestV2\SeverityLevel;

/**
 * Avalara Customer model
 *
 * @method string getId()
 * @method string setId()
 *
 * @method string getCustomerCode()
 * @method string setCustomerCode()
 *
 * @method string getName()
 * @method string setName()
 *
 * @method string getEmailAddress()
 * @method string setEmailAddress()
 *
 * @method string getLine1()
 * @method string setLine1()
 *
 * @method string getCity()
 * @method string setCity()
 *
 * @method string getCountry()
 * @method string setCountry()
 *
 * @method string getRegion()
 * @method string setRegion()
 *
 * @method string getPostalCode()
 * @method string setPostalCode()
 *
 * @category   OnePica
 * @package    OnePica_AvaTaxAr2
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Service_Avatax_Model_Customer extends Varien_Object
{
    public function __construct($model = array())
    {
        if($model instanceof \Avalara\AvaTaxRestV2\CustomerModel) {
            $this->setId($model->getId());
            $this->setCustomerCode($model->customerCode);
            $this->setName($model->name);
            $this->setEmailAddress($model->emailAddress);
            $this->setLine1($model->line1);
            $this->setCity($model->city);
            $this->setCountry($model->country);
            $this->setRegion($model->region);
            $this->setPostalCode($model->postalCode);
        } else {
            foreach ($model as $key => $value) {
                $parts = preg_split('/(?=[A-Z])/',$key);
                $newKey = implode('_', $parts);
                $newKey = strtolower($newKey);
                $this->setData($newKey, $value);
            }
        }

        return $this;
    }

    public function toAvalaraCustomerModel()
    {
        $avaCustomer = new \Avalara\AvaTaxRestV2\CustomerModel();
        $avaCustomer->customerCode = $this->getCustomerCode();
        $avaCustomer->name = $this->getName();
        $avaCustomer->emailAddress = $this->getEmailAddress();
        $avaCustomer->line1 = $this->getLine1();
        $avaCustomer->city = $this->getCity();
        $avaCustomer->country = $this->getCountry();
        $avaCustomer->region = $this->getRegion();
        $avaCustomer->postalCode = $this->getPostalCode();

        if ($this->getId()) {
            $avaCustomer->id = $this->getId();
        }

        return $avaCustomer;
    }
}