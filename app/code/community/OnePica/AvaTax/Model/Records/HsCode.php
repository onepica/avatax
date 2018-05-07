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
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * HS code model
 *
 * @method int getId()
 * @method $this setId(int $id)
 * @method string getHsCode()
 * @method $this setHsCode(string $hsCode)
 * @method string getDescription()
 * @method $this setDescription(string $description)
 * @method OnePica_AvaTax_Model_Records_Mysql4_HsCode getResource()
 * @method OnePica_AvaTax_Model_Records_Mysql4_HsCode_Collection getCollection()
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Records_HsCode extends Mage_Core_Model_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('avatax_records/hsCode');
    }

    /**
     * Get HS Code for particular country code
     *
     * @param $countryCode
     * @return Varien_Object
     */
    public function getCodeForCountry($countryCode)
    {
        /* @var OnePica_AvaTax_Model_Records_Mysql4_HsCodeCountry_Collection $collection */
        $collection = Mage::getModel('avatax_records/hsCodeCountry')->getCollection();
        $collection->addFilter('hs_id', $this->getId());
        $collection->getSelect()->where('country_codes REGEXP ?', $countryCode);

        return $collection->getFirstItem();
    }
}
