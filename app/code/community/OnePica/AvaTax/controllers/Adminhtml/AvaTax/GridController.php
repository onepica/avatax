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

/**
 * Admin grid controller
 *
 * @property \Mage_Adminhtml_Model_Session _sessionAdminhtml
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Adminhtml_AvaTax_GridController extends Mage_Adminhtml_Controller_Action
{
    /** @var null|\Mage_Adminhtml_Model_Session $_sessionAdminhtml */
    protected $_sessionAdminhtml = null;

    /**
     * Additional initialization
     */
    protected function _construct()
    {
        $this->setUsedModuleName('OnePica_AvaTax');

        $this->_sessionAdminhtml = Mage::getSingleton('adminhtml/session');
    }

    /**
     * Clear queue action
     *
     * @return $this
     */
    public function clearQueueAction()
    {
        Mage::getModel('avatax_records/queue_process')->clear();
        $this->_redirect('*/*/queue');

        return $this;
    }

    /**
     * Log action
     *
     * @return $this
     */
    public function logAction()
    {
        $this->_setTitle($this->__('Sales'))->_setTitle($this->__('Tax'))->_setTitle($this->__('AvaTax Log'));

        $this->loadLayout()
             ->_setActiveMenu('sales/tax/avatax_log')
             ->renderLayout();

        return $this;
    }

    /**
     * Log view action
     *
     * @return $this
     * @throws \Mage_Core_Exception
     */
    public function logViewAction()
    {
        $this->_setTitle($this->__('Sales'))->_setTitle($this->__('Tax'))->_setTitle($this->__('AvaTax Log'));

        $logId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('avatax/records_log')->load($logId);

        if (!$model->getId()) {
            $this->_redirect('*/*/log');

            return $this;
        }

        Mage::register('current_event', $model);

        $this->loadLayout()
             ->_setActiveMenu('sales/tax/avatax_log')
             ->renderLayout();

        return $this;
    }

    /**
     * Queue action
     *
     * @return $this
     */
    public function queueAction()
    {
        $this->_setTitle($this->__('Sales'))->_setTitle($this->__('Tax'))->_setTitle($this->__('AvaTax Queue'));

        $this->loadLayout()
             ->_setActiveMenu('sales/tax/avatax_queue')
             ->renderLayout();

        return $this;
    }

    /**
     * Process queue action
     *
     * @return $this
     * @throws \Varien_Exception
     */
    public function processQueueAction()
    {
        Mage::getModel('avatax_records/queue_process')->run();
        $this->_redirect('*/*/queue');

        return $this;
    }

    /**
     * HS Codes action
     *
     * @return $this
     */
    public function hscodeAction()
    {
        $this->_setTitle($this->__('Sales'))
             ->_setTitle($this->__('Tax'))
             ->_setTitle($this->__('AvaTax HS Codes'));

        $this->loadLayout()
             ->_setActiveMenu('sales/tax/avatax_hscode')
             ->renderLayout();

        return $this;
    }

    public function hscodeEditAction()
    {
        $hsCodeId = $this->getRequest()->getParam('id');
        $hsCodeModel = Mage::getModel('avatax_records/hsCode')->load($hsCodeId);

        if ($hsCodeModel->getId() || $hsCodeId == 0) {
            try {
                Mage::register('hsCode_data', $hsCodeModel);

                $this->loadLayout()->_setActiveMenu('sales/tax/avatax_hscode');

                $this->_addContent($this->getLayout()->createBlock('avatax/adminhtml_landedcost_hsCode_edit'))
                     ->_addLeft($this->getLayout()->createBlock('avatax/adminhtml_landedcost_hsCode_edit_tabs'));

                $this->renderLayout();
            } catch (Mage_Core_Exception $e) {
                $this->_sessionAdminhtml->addError($e->getMessage());
            }
        } else {
            $this->_sessionAdminhtml->addError($this->__('Item does not exist'));
            $this->_redirect('*/*/hscode');
        }
    }

    /**
     * HS code new action
     */
    public function hscodeNewAction()
    {
        $this->_forward('hscodeEdit');
    }

    /**
     * HS code save action
     *
     * @throws \Varien_Exception
     */
    public function hscodeSaveAction()
    {
        $postData = $this->getRequest()->getPost();

        if ($postData) {
            try {
                /** @var \OnePica_AvaTax_Model_Records_HsCode $hsCodeModel */
                $hsCodeModel = Mage::getModel('avatax_records/hsCode');

                $hsCodeModel->setId($this->getRequest()->getParam('id'))
                            ->setHsCode($postData['hs_code'])
                            ->setDescription($postData['description'])
                            ->save();

                $this->_sessionAdminhtml->addSuccess($this->__('Item was successfully saved'));
                $this->_sessionAdminhtml->setHsCodeData(false);
            } catch (Exception $e) {
                $this->_sessionAdminhtml->addError($e->getMessage());
                $this->_sessionAdminhtml->setHsCodeData($postData);

                $this->_redirect('*/*/hscodeEdit', array('id' => $this->getRequest()->getParam('id')));
            }
        }

        $this->_redirect('*/*/hscode');
    }

    /**
     * HS code delete action
     */
    public function hscodeDeleteAction()
    {
        if (0 < $this->getRequest()->getParam('id')) {
            try {
                /** @var \OnePica_AvaTax_Model_Records_HsCode $hsCodeModel */
                $hsCodeModel = Mage::getModel('avatax_records/hsCode');

                $hsCodeModel->setId($this->getRequest()->getParam('id'))
                            ->delete();

                $this->_sessionAdminhtml->addSuccess($this->__('Item was successfully deleted'));
            } catch (Exception $e) {
                $this->_sessionAdminhtml->addError($e->getMessage());
                $this->_redirect('*/*/hscodeEdit', array('id' => $this->getRequest()->getParam('id')));
            }
        }

        $this->_redirect('*/*/hscode');
    }

    /**
     * HS Codes action
     *
     * @return $this
     */
    public function hscodeMassDeleteAction()
    {
        $hscodeIds = $this->getRequest()->getParam('hscode');

        if (!is_array($hscodeIds)) {
            $this->_sessionAdminhtml->addError(Mage::helper('adminhtml')->__('Please select  HS code(s).'));
        } else {
            try {
                /** @var \Mage_Core_Model_Resource_Transaction $transaction */
                $transaction = Mage::getModel('core/resource_transaction');

                /** @var \OnePica_AvaTax_Model_Records_HsCode $hscodeModel */
                $hscodeModel = Mage::getModel('avatax_records/hsCode');

                foreach ($hscodeIds as $hscodeId) {
                    $hscode = clone $hscodeModel;
                    $hscode->load($hscodeId);
                    $transaction->addObject($hscode);
                }

                $transaction->delete();

                $this->_sessionAdminhtml->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($hscodeIds))
                );
            } catch (Exception $e) {
                $this->_sessionAdminhtml->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/hscode');

        return $this;
    }

    /**
     * HS Codes for countries mass delete action
     *
     * @return $this
     */
    public function hscodecountriesMassDeleteAction()
    {
        $hscodecountriesIds = $this->getRequest()->getParam('hscodecountries');
        $hscodeId = $this->getRequest()->getParam('hscode_id');

        if (!is_array($hscodecountriesIds)) {
            $this->_sessionAdminhtml->addError(Mage::helper('adminhtml')->__('Please select HS code(s).'));
        } else {
            try {
                /** @var \Mage_Core_Model_Resource_Transaction $transaction */
                $transaction = Mage::getModel('core/resource_transaction');

                /** @var \OnePica_AvaTax_Model_Records_HsCodeCountry $hscodeModel */
                $hscodecountriesModel = Mage::getModel('avatax_records/hsCodeCountry');

                foreach ($hscodecountriesIds as $hscodecountriesId) {
                    $hscodecountries = clone $hscodecountriesModel;
                    $hscodecountries->load($hscodecountriesId);
                    $transaction->addObject($hscodecountries);
                }

                $transaction->delete();

                $this->_sessionAdminhtml->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($hscodecountriesIds))
                );
            } catch (Exception $e) {
                $this->_sessionAdminhtml->addError($e->getMessage());
            }
        }

        $this->_redirect(
            '*/*/hscodeEdit', array(
                'id'         => $hscodeId,
                'active_tab' => 'grid_section'
            )
        );

        return $this;
    }

    /**
     * HS Codes for countries new action
     */
    public function hscodecountriesNewAction()
    {
        $this->_forward('hscodecountriesEdit');
    }

    /**
     * HS Codes for countries edit action
     */
    public function hscodecountriesEditAction()
    {
        $hsCodeId = $this->getRequest()->getParam('hs_code_id');
        $hsCodeCountryId = $this->getRequest()->getParam('id');
        /** @var \OnePica_AvaTax_Model_Records_HsCodeCountry $hsCodeCountryModel */
        $hsCodeCountryModel = Mage::getModel('avatax_records/hsCodeCountry')->load($hsCodeCountryId);

        if ($hsCodeCountryModel->getId() || $hsCodeCountryId == 0) {
            try {
                Mage::register('hs_code_countries_data', $hsCodeCountryModel);

                $this->loadLayout()->_setActiveMenu('sales/tax/avatax_hscode');

                $this->_addContent(
                    $this->getLayout()->createBlock('avatax/adminhtml_landedcost_hsCode_edit_tab_countries_edit')
                );
                $this->renderLayout();
            } catch (Mage_Core_Exception $e) {
                $this->_sessionAdminhtml->addError($e->getMessage());
            }
        } else {
            $this->_sessionAdminhtml->addError($this->__('Item does not exist'));

            $this->_redirect(
                '*/*/hscodeEdit', array(
                    'id'         => $hsCodeId,
                    'active_tab' => 'grid_section'
                )
            );
        }
    }

    /**
     * HS Codes for countries delete action
     */
    public function hscodecountriesDeleteAction()
    {
        $hsCodeId = $this->getRequest()->getParam('hs_code_id');
        $hsCodeCountryId = $this->getRequest()->getParam('id');

        if (0 < $hsCodeCountryId) {
            try {
                /** @var \OnePica_AvaTax_Model_Records_HsCodeCountry $hsCodeCountryModel */
                $hsCodeCountryModel = Mage::getModel('avatax_records/hsCodeCountry')->load($hsCodeCountryId);

                $hsCodeCountryModel->setId($hsCodeCountryId)->delete();

                $this->_sessionAdminhtml->addSuccess($this->__('Item was successfully deleted'));
            } catch (Exception $e) {
                $this->_sessionAdminhtml->addError($e->getMessage());
                $this->_redirect(
                    '*/*/hscodecountriesEdit', array(
                        'id'         => $hsCodeCountryId,
                        'hs_code_id' => $hsCodeId
                    )
                );
            }
        }

        $this->_redirect(
            '*/*/hscodeEdit', array(
                'id'         => $hsCodeId,
                'active_tab' => 'grid_section'
            )
        );
    }

    /**
     * HS Codes for countries save action
     *
     * @throws \Varien_Exception
     */
    public function hscodecountriesSaveAction()
    {
        $hsCodeId = $this->getRequest()->getParam('hs_code_id');
        $hsCodeCountryId = $this->getRequest()->getParam('id');

        if ($this->getRequest()->getPost()) {
            try {
                /** @var \OnePica_AvaTax_Model_Records_HsCodeCountry $hsCodeCountryModel */
                $hsCodeCountryModel = Mage::getModel('avatax_records/hsCodeCountry');

                $hsCodeCountryModel->setId($hsCodeCountryId)
                                   ->setHsId($hsCodeId)
                                   ->setHsFullCode($this->getRequest()->getPost('hs_full_code'))
                                   ->setCountryCodes($this->getRequest()->getPost('country_codes'))
                                   ->save();

                $this->_sessionAdminhtml->addSuccess($this->__('Item was successfully saved'));
                $this->_sessionAdminhtml->setHsCodeCountriesData(false);
            } catch (Exception $e) {
                $this->_sessionAdminhtml->addError($e->getMessage());
                $this->_sessionAdminhtml->setHsCodeCountriesData($this->getRequest()->getPost());

                $this->_redirect(
                    '*/*/hscodecountriesEdit', array(
                        'id'         => $hsCodeCountryId,
                        'hs_code_id' => $hsCodeId
                    )
                );
            }
        }

        $this->_redirect(
            '*/*/hscodeEdit', array(
                'id'         => $hsCodeId,
                'active_tab' => 'grid_section'
            )
        );
    }

    /**
     * Check if is allowed
     *
     * @return bool
     * @throws \Varien_Exception
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('avatax');
    }

    /**
     * Magento <1.4 does not let the title be set
     *
     * @param string $title
     * @return $this
     */
    protected function _setTitle($title)
    {
        if (method_exists($this, '_title')) {
            $this->_title($title);
        }

        return $this;
    }

    /**
     * Set Pending Status For Queue item with failed status
     *
     * @return $this
     */
    public function setPendingStatusForQueueItemAction()
    {
        $itemId = $this->getRequest()->getParam('queue_id');
        if ($itemId) {
            try {
                // init model and update status
                /** @var $model OnePica_AvaTax_Model_Records_Queue */
                $model = Mage::getModel('avatax/records_queue');
                $model->load($itemId);
                if (!$model->getId()
                    || $model->getStatus() != OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED
                ) {
                    Mage::throwException($this->__('Unable to find a queue item #%s. with Failed status', $itemId));
                }

                $model->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_PENDING);
                $model->setAttempt(0);
                $model->save();

                // display success message
                $this->_getSession()->addSuccess($this->__('Queue item #%s status has been updated.', $model->getId()));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    $this->__('An error occurred while updating queue item status.')
                );
            }
        }

        $this->_redirectReferer();

        return $this;
    }
}
