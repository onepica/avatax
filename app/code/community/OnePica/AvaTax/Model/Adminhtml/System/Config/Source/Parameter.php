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
 * Parameter source config model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Adminhtml_System_Config_Source_Parameter
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /** @var OnePica_AvaTax_Model_Records_Mysql4_Parameter_Collection $collection */
        $collection = Mage::getModel('avatax_records/parameter')->getCollection()->load();
        $result = array();
        array_push($result, array('value' => null, 'label' => ' '));
        /** @var OnePica_AvaTax_Model_Records_Parameter $item */
        foreach ($collection as $item) {
            array_push($result,
                array(
                    'value' => $item->getId(),
                    'label' => $item->getDescription()
                )
            );
        }

        return $result;
    }
}
