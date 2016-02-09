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
 * Field list source model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Source_Fieldlist
{
    /**
     * Gets the list of required fileds for the admin config dropdown
     *
     * @return array
     */
    public function toOptionArray()
    {
        $obj = Mage::getConfig()->getNode('admin/fieldsets/customer_dataflow');

        foreach ($obj as $obj2) {
            foreach ($obj2 as $key2 => $obj3) {
                if ($obj3->shipping) {
                    $arr[] = array(
                        'value' => $key2,
                        'label' => Mage::helper('avatax')->__($key2),
                    );
                }
            }
        }

        return $arr;
    }
}
