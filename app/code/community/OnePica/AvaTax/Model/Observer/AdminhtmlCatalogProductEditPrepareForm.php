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
 * Avatax Observer SalesConvertQuoteAddressToOrderAddress
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_AdminhtmlCatalogProductEditPrepareForm
    extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Save quote address id to Mage_Sales_Model_Order_Address
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws \Varien_Exception
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $form = $observer->getForm();
        if (!empty($form)) {
            $elements = $form->getElements();
            if (!empty($elements) && $elements->count() > 0) {
                switch ($elements[0]->getLegend()) {
                    case OnePica_AvaTax_Helper_LandedCost::AVATAX_PRODUCT_GROUP_LANDED_COST:
                        {
                            $parameter = $form->getElement(
                                OnePica_AvaTax_Helper_LandedCost::AVATAX_PRODUCT_LANDED_COST_ATTR_PARAMETER
                            );
                            if ($parameter) {
                                $parameter->setRenderer(
                                    Mage::app()->getLayout()->createBlock(
                                        'avatax/adminhtml_catalog_product_edit_tab_landedCost_parameter'
                                    )
                                );
                            }
                        }
                        break;
                }
            }
        }

        return $this;
    }
}
