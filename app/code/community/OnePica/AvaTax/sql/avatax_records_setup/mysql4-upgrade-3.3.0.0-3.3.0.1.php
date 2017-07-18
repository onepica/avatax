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

/** @var $this Mage_Core_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');

$attributeCode = 'avatax_customer_code';

$setup->addAttribute(
    'customer', $attributeCode, array(
    'label'             => 'Avatax Customer Code',
    'type'              => 'varchar',
    'input'             => 'text',
    'visible'           => 1,
    'required'          => 0,
    'user_defined'      => 1,
    'visible_on_front'  => 0,
    'position'          => 200,
    'sort_order'        => 200,
    )
);

// update options for attribute
$attribute = Mage::getModel('eav/config')->getAttribute('customer', $attributeCode);
$attribute->setData('is_system', 0);
$attribute->setData('is_visible', 0);
$attribute->setData('used_in_forms', array('adminhtml_customer'));
$attribute->setData('validate_rules', array('max_text_length'   => 50));
$attribute->save();

$installer->endSetup();
