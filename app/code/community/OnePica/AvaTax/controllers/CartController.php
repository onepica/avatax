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
 * Shopping cart controller
 */

require_once 'Mage/Checkout/controllers/CartController.php';

class OnePica_AvaTax_CartController extends Mage_Checkout_CartController 
{
    /**
     * Initialize shipping information
     */
    public function estimatePostAction()
    {   
		$session = Mage::getSingleton('checkout/session');
		$session->setPostType('estimate');
		parent::estimatePostAction();		
    }
}
