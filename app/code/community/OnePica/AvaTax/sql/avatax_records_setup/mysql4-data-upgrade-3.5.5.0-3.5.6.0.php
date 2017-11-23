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

/** @var \Magento_Db_Adapter_Pdo_Mysql $adapter */
$adapter = $this->getConnection();
$taxClassTableName = $this->getTable('tax/tax_class');

$adapter->insert(
    $taxClassTableName, array(
        'class_name'     => 'AvaTax. Non Taxable Product',
        'op_avatax_code' => 'NT',
        'class_type'     => 'PRODUCT',
    )
);

$this->endSetup();
