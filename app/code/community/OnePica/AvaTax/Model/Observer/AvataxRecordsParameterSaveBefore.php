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

/**
 * Avatax Observer RecordsParameterSaveBefore
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_AvataxRecordsParameterSaveBefore extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Validate Model Before Save
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws \OnePica_AvaTax_Exception
     * @throws \Varien_Exception
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var OnePica_AvaTax_Helper_LandedCost $helper */
        $helper = Mage::helper('avatax/landedCost');

        /** @var OnePica_AvaTax_Model_Records_Parameter $unit */
        $unit = $observer->getEvent()->getObject();
        if ($helper->isMassType($unit)) {
            $uom = $unit->getAvalaraUom();
            $isValid = !empty($uom);
            if (!$isValid) {
                throw new \OnePica_AvaTax_Exception(
                    Mage::helper('avatax')->__('UOM field is required for this Parameter.')
                );
            }
        }

        return $this;
    }
}
