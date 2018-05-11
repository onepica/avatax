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

/** @var \Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
/** @var \Varien_Db_Adapter_Pdo_Mysql $conn */
$conn = $installer->getConnection();

$table = $this->getTable('avatax_records/agreement');

if ($conn->showTableStatus($table) !== false) {
    /** @var OnePica_AvaTax_Model_Records_Mysql4_Agreement_Collection $collection */
    $collection = Mage::getModel('avatax_records/agreement')->getCollection();
    $collection->load();

    if ($collection->count() == 0) {
        /** @var Mage_Adminhtml_Helper_Data $helper */
        $helper = Mage::helper('adminhtml');

        // NAFTA Agreement
        {
            /** @var $agreement OnePica_AvaTax_Model_Records_Agreement */
            $agreement = Mage::getModel('avatax_records/agreement');
            $agreement->setAvalaraAgreementCode('NAFTA');
            $agreement->setDescription($helper->__('North American Free Trade Agreement'));
            $agreement->setCountryList('US,CA,MX');

            $collection->addItem($agreement);
        }

        $collection->save();
    }
}

$installer->endSetup();
