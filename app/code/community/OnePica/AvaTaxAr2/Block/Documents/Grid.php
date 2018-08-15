<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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
 * Class OnePica_AvaTaxAr2_Block_Documents_Grid
 */
class OnePica_AvaTaxAr2_Block_Documents_Grid extends Mage_Core_Block_Template
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();

        /** @var \OnePica_AvaTaxAr2_Block_Documents_Grid $rootBlock */
        $rootBlock = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root');

        $rootBlock->setHeaderTitle($this->__('AvaTax Documents'));
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        try {
            $pager = $this->getLayout()
                          ->createBlock('page/html_pager', 'avataxar2.documents.grid.pager')
                          ->setCollection($this->getCollection());
            $this->setChild('pager', $pager);
            $this->getCollection()->load();
        } catch (Exception $exception) {
            $this->_getCoreSession()->addError($exception->getMessage());
        }

        return $this;
    }

    /**
     * @return \OnePica_AvaTaxAr2_Model_Records_RestV2_Document_Collection
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_collection = new Varien_Data_Collection();
            if ($this->getCustomerNumber() !== null) {
                $this->_collection = Mage::getModel('avataxar2_records/document')->getCollection();
                $this->_collection->addCustomerFilter($this->getCustomerNumber());
            }
        }

        return $this->_collection;
    }

    /**
     * @return int|string
     */
    public function getCustomerNumber()
    {
        return $this->_getHelper()->getCustomerNumber($this->getCustomer());
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param OnePica_AvaTaxAr2_Model_Records_RestV2_Document $document
     * @return string
     */
    public function getRevokeUrl($document)
    {
        return $this->getUrl('*/*/delete', array('document_id' => $document->getId()));
    }

    /**
     * @param OnePica_AvaTaxAr2_Model_Records_RestV2_Document $document
     * @return string
     */
    public function getDocumentPdfUrl($document)
    {
        return $this->getUrl('*/*/documentGetPDF', array('document_id' => $document->getId()));
    }

    /**
     * @param OnePica_AvaTaxAr2_Model_Records_RestV2_Document $document
     * @return string
     */
    public function getDocumentPdfName($document)
    {
        return $document->getFilename();
    }

    /**
     * @return \Mage_Customer_Model_Customer
     */
    protected function getCustomer()
    {
        return $this->_getCustomerSession()->getCustomer();
    }

    /**
     * @return \Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return \Mage_Core_Model_Session
     */
    protected function _getCoreSession()
    {
        return Mage::getSingleton('core/session');
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avataxar2');
    }

    /**
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isEnabled()
    {
        /** @var OnePica_AvaTaxAr2_Helper_Config $config */
        $config = Mage::helper('avataxar2/config');

        return $config->isEnabled();
    }
}
