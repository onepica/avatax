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
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');

        $this->setUseAjax(true);

        $this->setEmptyText(Mage::helper('customer')->__('No Exemptions Found'));
    }

    /**
     * @return \Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $url = $this->getUrl(
            'adminhtml/avaTaxAr2_popup/genCert', array(
                'customerId'     => $this->_getCustomer()->getId(),
                'customerNumber' => $this->_getCustomerNumberOrGenerate()
            )
        );
        $this->setChild(
            'add_cert_button',
            $this->getLayout()
                 ->createBlock('adminhtml/widget_button')
                 ->setData(
                     array(
                         'label'   => $this->__('New Certificate'),
                         'onclick' => 'AvaTaxCert.showPopup(\'' . $url . '\', \'' .
                             $this->__('Create Exempt Certificate') . '\')',
                         'class'   => 'add'
                     )
                 )
        );

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();
        $html .= $this->getNewCertificateButtonHtml();

        return $html;
    }

    /**
     * @return string
     */
    public function getNewCertificateButtonHtml()
    {
        return $this->getChildHtml('add_cert_button');
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
            $collection = new Varien_Data_Collection();
            if ($this->_getCustomerNumberAttribute() !== null) {
                /** @var OnePica_AvaTaxAr2_Model_Records_RestV2_Document_Collection $collection */
                $collection = Mage::getModel('avataxar2_records/document')->getCollection();
                $collection->addCustomerFilter($this->_getCustomerNumberAttribute());
            }

            $this->setCollection($collection);

            $sort = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $page = $this->getParam($this->getVarNamePage(), $this->_defaultPage);
            $limit = $this->getParam($this->getVarNameLimit(), $this->_defaultLimit);
            $filter = $this->getParam($this->getVarNameFilter(), $this->_defaultFilter);

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            } elseif (is_array($filter)) {
                $this->_setFilterValues($filter);
            }

            if (isset($this->_columns[$sort]) && $this->_columns[$sort]->getIndex()) {
                $dir = (strtolower($dir) == 'desc') ? 'desc' : 'asc';
                $this->_columns[$sort]->setDir($dir);
            }

            $collection->setOrder($sort, $dir)
                       ->setCurPage($page)
                       ->setPageSize($limit);

            $this->_preparePage();
        } catch (Exception $exception) {
            $session = $this->_getAdminhtmlSession();
            $session->addError($exception->getMessage());
        }

        return $this;
    }

    /**
     * @param $column
     * @return $this|\Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ($column->getFilterIndex()) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } else {
                $cond = $column->getFilter()->getValue();
                if ($field && isset($cond)) {
                    $this->getCollection()->addFilter($field, $cond);
                }
            }
        }

        return $this;
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
            'exposureZone', array(
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
                'header'   => $this->__('Signed Date'),
                'index'    => 'signedDate',
                'type'     => 'datetime',
                'align'    => 'left',
                'filter'   => false,
                'sortable' => false,
            )
        );

        $this->addColumn(
            'expirationDate', array(
                'header'   => $this->__('Expiration Date'),
                'index'    => 'expirationDate',
                'type'     => 'datetime',
                'align'    => 'left',
                'filter'   => false,
                'sortable' => false,
            )
        );

        $customerId = $this->_getHelper()->getCustomerNumber($this->_getCustomer());

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
                        'caption' => $this->__('Revoke'),
                        'onClick' => 'return AvaTaxCert.delete(' . implode(
                                ', ',
                                array(
                                    '\'$id\'',
                                    '\'' . $customerId . '\'',
                                    '\'' . $this->getUrl('adminhtml/avaTaxAr2_grid/documentDelete') . '\'',
                                    $this->getJsObjectName()
                                )
                            ) . ')',
                        'url'     => '#'
                    ),
                ),
            )
        );

        $pdfUrl = $this->getUrl('adminhtml/avaTaxAr2_grid/documentGetPDF', array('id' => '$id'));
        $this->addColumn(
            'actions_view', array(
                'header'   => '',
                'align'    => 'center',
                'type'     => 'action',
                'width'    => '10px',
                'filter'   => false,
                'sortable' => false,
                'actions'  => array(
                    array(
                        'caption' => $this->__('View'),
                        'url'     => $pdfUrl,
                        'field'   => 'id',
                        'popup'   => true,
                    ),
                ),
            )
        );

        $this->addColumn(
            'actions_download', array(
                'header'   => '',
                'align'    => 'center',
                'type'     => 'action',
                'width'    => '10px',
                'filter'   => false,
                'sortable' => false,
                'actions'  => array(
                    array(
                        'caption'  => $this->__('Download'),
                        'url'      => $pdfUrl,
                        'field'    => 'id',
                        'target'   => '_blank',
                        'download' => '$filename'
                    ),
                ),
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid mass action
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws \Exception
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('documents');

        $data = array(
            'customerId'   => $this->_getCustomer()->getId(),
            'customerCode' => $this->_getHelper()->getCustomerNumber($this->_getCustomer()),
            'activeTab'    => $this->getRequest()->getParam('tab')
        );
        $this->getMassactionBlock()->addItem(
            'delete', array(
                'label'   => $this->__('Revoke'),
                'url'     => $this->getUrl('adminhtml/avaTaxAr2_grid/documentMassDelete', $data),
                'confirm' => $this->__('Are you sure?')
            )
        );

        return $this;
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::registry('current_customer');
    }

    /**
     * @return string
     */
    protected function _getCustomerNumberAttribute()
    {
        return $this->_getHelper()->getCustomerNumber($this->_getCustomer());
    }

    /**
     * @return string
     */
    protected function _getCustomerNumberOrGenerate()
    {
        return $this->_getHelper()->getCustomerNumberOrGenerate($this->_getCustomer());
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avataxar2');
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avataxar2/config');
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
