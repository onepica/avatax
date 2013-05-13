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


require_once 'Mage/Checkout/controllers/OnepageController.php';

class OnePica_AvaTax_OnepageController extends Mage_Checkout_OnepageController
{
    /**
     * Initialize shipping information
     */
    public function indexAction()
    {         
		$session = Mage::getSingleton('checkout/session');
		$session->setPostType('onepage');
		parent::indexAction();		
    }
}
