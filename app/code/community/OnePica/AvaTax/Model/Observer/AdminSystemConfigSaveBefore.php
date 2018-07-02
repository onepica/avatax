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
 * Avatax Observer AdminSystemConfigSaveBefore
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_AdminSystemConfigSaveBefore extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     *  Validates AvaTax configuration
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Model_Config_Data $config */
        $config = $observer->getEvent()->getObject();
        if ($config) {
            switch ($config->getSection()) {
                case 'tax':
                    {
                        $isPriceIncludeTax = (bool)$config->getData('groups/calculation/fields/price_includes_tax/value');
                        $isLandedCostEnabled = (bool)$config->getData('groups/avatax_landed_cost/fields/landed_cost_enabled/value');
                        if ($isLandedCostEnabled && $isPriceIncludeTax) {
                            throw new \OnePica_AvaTax_Exception(
                                Mage::helper('avatax')->__(
                                    'You can only use \'Excluding Tax\' calculation model for \'Catalog Prices\' when \'Customs Duty\' feature is enabled.'
                                ));
                        }
                    }
                    break;
            }
        }

        return $this;
    }
}
