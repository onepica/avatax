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
/** @var  \Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

/** @var \Mage_Eav_Model_Entity_Attribute $attr */
$attr = Mage::getModel('eav/config')->getAttribute('customer', OnePica_AvaTaxAr2_Helper_Data::AVATAX_CUSTOMER_CODE);

if ($attr->getId()) {
    $attr->setData('used_in_forms', array(OnePica_AvaTaxAr2_Helper_Data::AVATAX_CUSTOMER_DOCUMENTS_FORM_CODE));
    $attr->setData('sort_order', 10);
    $attr->setData('is_user_defined', 0);
    $attr->setData('is_system', 1);
    $attr->setData('is_visible_on_front', 0);
    $attr->setData('frontend_label', Mage::helper('avataxar2')->__('Customer Number'));
    $attr->save();
}

$installer->endSetup();
