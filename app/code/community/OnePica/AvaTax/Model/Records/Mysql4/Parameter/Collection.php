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
 * Parameter collection model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Records_Mysql4_Parameter_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('avatax_records/parameter');
    }

    public function getUnitForCountry($ids, $countryCode)
    {
        $collection = Mage::getModel('avatax_records/parameter')
            ->getCollection()
            ->addFieldToFilter('id', array('in' => $ids));
        $collection->getSelect()->where('country_list REGEXP ?', $countryCode);

        /** @var OnePica_AvaTax_Model_Records_Parameter $unit */
        $unit = $collection->getFirstItem();

        return $unit;
    }
}
