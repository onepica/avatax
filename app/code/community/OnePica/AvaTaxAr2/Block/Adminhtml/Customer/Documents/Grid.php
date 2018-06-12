<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Documents grid block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class OnePica_AvaTaxAr2_Block_Adminhtml_Customer_Documents_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('exemptionGrid');
        $this->setDefaultSort('start_at');
        $this->setDefaultDir('desc');

        $this->setUseAjax(true);

        $this->setEmptyText(Mage::helper('customer')->__('No Exemptions Found'));
    }

    /**
     * Grid url getter
     *
     * @deprecated after 1.3.2.3 Use getAbsoluteGridUrl() method instead
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/avaTaxAr2_grid/documents', array('_current' => true));
    }

    /**
     * Prepare grid collection object
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        try {
            $exempt = $this->_getCustomer()->getData(OnePica_AvaTaxAr2_Helper_Data::AVATAX_CUSTOMER_EXEMPTION_NUMBER);

            /** @var OnePica_AvaTaxAr2_Model_Records_RestV2_Document_Collection $collection */
            $collection = Mage::getModel('avataxar2_records/document')->getCollection();

            $collection->addCustomerFilter($exempt);

            $this->setCollection($collection);
        } catch (Exception $exception) {
            $session = $this->_getAdminhtmlSession();
            $session->addError($exception->getMessage());
        }

        return parent::_prepareCollection();
    }

    /**
     * @return \Mage_Adminhtml_Block_Widget_Grid
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id', array(
                'header' => $this->__('ID'),
                'index'  => 'id',
                'align'  => 'left',
                'width'  => 10
            )
        );

        $this->addColumn(
            'valid', array(
                'header'  => $this->__('Valid'),
                'index'   => 'valid',
                'type'    => 'options',
                'options' => array(0 => $this->__('No'), 1 => $this->__('Yes')),
                'align'   => 'center'
            )
        );

        $this->addColumn(
            'verified', array(
                'header'  => $this->__('Verified'),
                'index'   => 'verified',
                'type'    => 'options',
                'options' => array(0 => $this->__('No'), 1 => $this->__('Yes')),
                'align'   => 'center'
            )
        );

        $this->addColumn(
            'exposureZoneName', array(
                'header'   => $this->__('Exposure Zone'),
                'index'    => 'exposureZone',
                'type'     => 'text',
                'renderer' => 'avataxar2/adminhtml_customer_documents_grid_renderer_exposureZoneName',
                'align'    => 'left'
            )
        );

        $this->addColumn(
            'exemptionReason', array(
                'header'   => $this->__('Exemption Reason'),
                'index'    => 'exemptionReason',
                'type'     => 'text',
                'renderer' => 'avataxar2/adminhtml_customer_documents_grid_renderer_exemptionReasonName',
                'align'    => 'left'
            )
        );

        $this->addColumn(
            'signedDate', array(
                'header' => $this->__('Signed Date'),
                'index'  => 'signedDate',
                'type'   => 'datetime',
                'align'  => 'left'
            )
        );

        $this->addColumn(
            'expirationDate', array(
                'header' => $this->__('Expiration Date'),
                'index'  => 'expirationDate',
                'type'   => 'datetime',
                'align'  => 'left'
            )
        );

        $this->addColumn(
            'actions', array(
                'header'   => $this->__('Actions'),
                'align'    => 'center',
                'type'     => 'action',
                'width'    => '10px',
                'filter'   => false,
                'sortable' => false,
                'actions'  => array(
                    array(
                        'caption' => $this->__('Delete'),
                        'onClick' => 'return Documents.delete(\'$doc_id\')',
                        'url'     => '#',
                    ),
                ),
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::registry('current_customer');
    }

    /**
     * Get adminhtml model session
     *
     * @return \Mage_Adminhtml_Model_Session
     */
    protected function _getAdminhtmlSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
}
