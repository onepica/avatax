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

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

$installer->startSetup();

$installer->addAttribute('quote_address', 'avatax_landed_cost_import_duties_amount', array('type'=>'decimal'));
$installer->addAttribute('quote_address', 'base_avatax_landed_cost_import_duties_amount', array('type'=>'decimal'));

$installer->addAttribute('quote_item', 'avatax_landed_cost_import_duties_amount', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'base_avatax_landed_cost_import_duties_amount', array('type'=>'decimal'));

$installer->addAttribute('order', 'avatax_landed_cost_import_duties_amount', array('type'=>'decimal'));
$installer->addAttribute('order', 'base_avatax_landed_cost_import_duties_amount', array('type'=>'decimal'));

$installer->addAttribute('order_item', 'avatax_landed_cost_import_duties_amount', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'base_avatax_landed_cost_import_duties_amount', array('type'=>'decimal'));

$installer->endSetup();
