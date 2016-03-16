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
 * Class OnePica_AvaTax_Model_Observer_ControllerActionLayoutGenerateXmlBefore
 */
class OnePica_AvaTax_Model_Observer_ControllerActionLayoutGenerateXmlBefore
    extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Recalculates taxes during order creation in admin panel.
     * quote item is null during first collect totals,
     * that is why rate request does not send to avalara.
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $action = $observer->getAction();
        if ($action instanceOf Mage_Adminhtml_Sales_Order_CreateController
            && $action->getRequest()->getActionName() == 'loadBlock'
        ) {
            $requestBlocks = $action->getRequest()->getParam('block');
            if (isset($requestBlocks) && strstr($requestBlocks, 'totals')) {
                /* @var Mage_Adminhtml_Model_Session_Quote $adminQuote */
                $adminQuote = Mage::getSingleton('adminhtml/session_quote');
                /* @var Mage_Sales_Model_Quote $quote */
                $quote = (isset($adminQuote)) ? $adminQuote->getQuote() : null;
                if (isset($quote)) {
                    $quote->setTotalsCollectedFlag(false);
                    $quote->collectTotals();
                    $quote->save();
                }
            }
        }

        return $this;
    }
}
