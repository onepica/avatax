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
$installer = $this;
$this->startSetup();
/* @var $this Mage_Core_Model_Resource_Setup */
$adapter = $this->getConnection();
$adapter->update(
    $this->getTable('tax/tax_class'),
    array(
        'class_name' => 'Shipping (not used by AvaTax)'
    ),
    array(
        'class_name = ?' => 'Shipping',
        'class_type = ?' => Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
    )
);
$this->endSetup();
