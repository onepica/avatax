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
 * HS code for country model
 *
 * @method int getId()
 * @method $this setId(int $id)
 * @method int getHsId()
 * @method $this setHsId(int $hsId)
 * @method string getHsFullCode()
 * @method $this setHsFullCode(string $hsCode)
 * @method string getCountryCodes()
 * @method $this setCountryCodes(string $countryCodes)
 * @method OnePica_AvaTax_Model_Records_Mysql4_HsCodeCountry getResource()
 * @method OnePica_AvaTax_Model_Records_Mysql4_HsCodeCountry_Collection getCollection()
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Records_HsCodeCountry extends Mage_Core_Model_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('avatax_records/hsCodeCountry');
    }
}
