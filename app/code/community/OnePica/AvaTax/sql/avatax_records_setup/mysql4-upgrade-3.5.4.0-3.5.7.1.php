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

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$attributeCode = 'avatax_hts_code';

/** @var $attribute Mage_Eav_Model_Entity_Attribute */
$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);

if (!$attribute->getId()) {
    $installer->addAttribute('catalog_product', $attributeCode, array(
        'type'             => 'varchar',
        'backend'          => '',
        'frontend'         => '',
        'label'            => 'Avatax HTS Code',
        'input'            => 'text',
        'class'            => '',
        'source'           => '',
        'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'          => 1,
        'required'         => 0,
        'user_defined'     => 1,
        'default'          => '',
        'searchable'       => 0,
        'filterable'       => 0,
        'comparable'       => 0,
        'visible_on_front' => 0,
        'unique'           => 0,
        'apply_to'         => 'simple,configurable,bundle',
        'is_configurable'  => 0,
        'group'      => 'Prices',
        'position'   => 200,
        'sort_order' => 200,
    ));
}
$installer->endSetup();
