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
    /**
     * Additional initialization
     */
    protected function _construct()
    {
        $this->setUsedModuleName('OnePica_AvaTax');
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
        $this->_setTitle($this->__('AvaTax'))->_setTitle($this->__('AvaTax Log'));

        $this->loadLayout()
             ->_setActiveMenu('avatax/avatax_log')
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
        $this->_setTitle($this->__('AvaTax'))->_setTitle($this->__('AvaTax Log'));

        $logId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('avatax/records_log')->load($logId);

        if (!$model->getId()) {
            $this->_redirect('*/*/log');

            return $this;
        }

        Mage::register('current_event', $model);

        $this->loadLayout()
             ->_setActiveMenu('avatax/avatax_log')
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
        $this->_setTitle($this->__('AvaTax'))->_setTitle($this->__('AvaTax Queue'));

        $this->loadLayout()
             ->_setActiveMenu('avatax/avatax_queue')
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
        $this->_setTitle($this->__('AvaTax'))
             ->_setTitle($this->__('Customs Duty'))
             ->_setTitle($this->__('AvaTax HS Code Groups'));

        $this->loadLayout()
             ->_setActiveMenu('avatax/landedcost/avatax_hscode')
             ->renderLayout();

        return $this;
    }

    /**
     * HS Codes edit action
     */
    public function hscodeEditAction()
    {
        $this->_setTitle($this->__('AvaTax'))
             ->_setTitle($this->__('Customs Duty'))
             ->_setTitle($this->__('AvaTax HS Code Group'));

        $hsCodeId = $this->getRequest()->getParam('id');
        $hsCodeModel = Mage::getModel('avatax_records/hsCode')->load($hsCodeId);

        if ($hsCodeModel->getId() || $hsCodeId == 0) {
            try {
                Mage::register('hsCode_data', $hsCodeModel);

                $this->loadLayout()->_setActiveMenu('avatax/landedcost/avatax_hscode');

                $this->_addContent($this->getLayout()->createBlock('avatax/adminhtml_landedcost_hsCode_edit'))
                     ->_addLeft($this->getLayout()->createBlock('avatax/adminhtml_landedcost_hsCode_edit_tabs'));

                $this->renderLayout();
            } catch (Mage_Core_Exception $e) {
                $this->getAdminhtmlSession()->addError($e->getMessage());
            }
        } else {
            $this->getAdminhtmlSession()->addError($this->__('Item does not exist'));
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
        $hsCodeId = $this->getRequest()->getParam('id');

        if (!$postData) {
            $this->_redirect('*/*/hscodeEdit', array('id' => $hsCodeId));
        }

        try {
            /** @var \OnePica_AvaTax_Model_Records_HsCode $hsCodeModel */
            $hsCodeModel = Mage::getModel('avatax_records/hsCode');

            $hsCodeModel->setId($hsCodeId)
                        ->setHsCode((string)$this->getRequest()->getPost('hs_code'))
                        ->setDescription((string)$this->getRequest()->getPost('description'))
                        ->save();

            $this->getAdminhtmlSession()->addSuccess($this->__('Item was successfully saved'));
            $this->getAdminhtmlSession()->setHsCodeData(false);

            $this->_redirectAfterSaveModel($hsCodeModel, '*/*/hscode');
        } catch (Exception $e) {
            $this->getAdminhtmlSession()->addError($e->getMessage());
            $this->getAdminhtmlSession()->setHsCodeData($postData);

            $this->_redirect('*/*/hscodeEdit', array('id' => $hsCodeId));
        }
    }

    /**
     * HS code delete action
     */
    public function hscodeDeleteAction()
    {
        $hsCodeId = $this->getRequest()->getParam('id');

        if ($hsCodeId <= 0) {
            $this->getAdminhtmlSession()->addError($this->__('HS code id is invalid'));
            $this->_redirect('*/*/hscode');
        }

        try {
            /** @var \OnePica_AvaTax_Model_Records_HsCode $hsCodeModel */
            $hsCodeModel = Mage::getModel('avatax_records/hsCode');

            $hsCodeModel->setId($hsCodeId)->delete();

            $this->getAdminhtmlSession()->addSuccess($this->__('Item was successfully deleted'));

            $this->_redirect('*/*/hscode');
        } catch (Exception $e) {
            $this->getAdminhtmlSession()->addError($e->getMessage());
            $this->_redirect('*/*/hscodeEdit', array('id' => $hsCodeId));
        }
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
            $this->getAdminhtmlSession()->addError(Mage::helper('adminhtml')->__('Please select HS code(s).'));
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

                $this->getAdminhtmlSession()->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($hscodeIds))
                );
            } catch (Exception $e) {
                $this->getAdminhtmlSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/hscode');

        return $this;
    }

    /**
     * HS Codes for Countries grid action
     */
    public function hscodecountriesGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('avatax/adminhtml_landedcost_hsCode_edit_tab_countries_grid')->toHtml()
        );
    }

    /**
     * HS Codes for Countries mass delete action
     *
     * @return $this
     */
    public function hscodecountriesMassDeleteAction()
    {
        $hscodecountriesIds = $this->getRequest()->getParam('hscodecountries');
        $hscodeId = $this->getRequest()->getParam('hscode_id');

        if (!is_array($hscodecountriesIds)) {
            $this->getAdminhtmlSession()->addError(Mage::helper('adminhtml')->__('Please select HS code(s).'));
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

                $this->getAdminhtmlSession()->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($hscodecountriesIds))
                );
            } catch (Exception $e) {
                $this->getAdminhtmlSession()->addError($e->getMessage());
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
     * HS Codes for Countries new action
     */
    public function hscodecountriesNewAction()
    {
        $this->_forward('hscodecountriesEdit');
    }

    /**
     * HS Codes for Countries edit action
     */
    public function hscodecountriesEditAction()
    {
        $this->_setTitle($this->__('AvaTax'))
             ->_setTitle($this->__('Customs Duty'))
             ->_setTitle($this->__('AvaTax HS Codes for Countries'));

        $hsCodeId = $this->getRequest()->getParam('hs_code_id');
        $hsCodeCountryId = $this->getRequest()->getParam('id');
        /** @var \OnePica_AvaTax_Model_Records_HsCodeCountry $hsCodeCountryModel */
        $hsCodeCountryModel = Mage::getModel('avatax_records/hsCodeCountry')->load($hsCodeCountryId);

        if ($hsCodeCountryModel->getId() || $hsCodeCountryId == 0) {
            try {
                Mage::register('hs_code_countries_data', $hsCodeCountryModel);

                $this->loadLayout()->_setActiveMenu('avatax/landedcost/avatax_hscode');

                $this->_addContent(
                    $this->getLayout()->createBlock('avatax/adminhtml_landedcost_hsCode_edit_tab_countries_edit')
                );
                $this->renderLayout();
            } catch (Mage_Core_Exception $e) {
                $this->getAdminhtmlSession()->addError($e->getMessage());
            }
        } else {
            $this->getAdminhtmlSession()->addError($this->__('Item does not exist'));

            $this->_redirect(
                '*/*/hscodeEdit', array(
                    'id'         => $hsCodeId,
                    'active_tab' => 'grid_section'
                )
            );
        }
    }

    /**
     * HS Codes for Countries delete action
     */
    public function hscodecountriesDeleteAction()
    {
        $hsCodeId = $this->getRequest()->getParam('hs_code_id');
        $hsCodeCountryId = $this->getRequest()->getParam('id');

        if ($hsCodeCountryId <= 0) {
            $this->getAdminhtmlSession()->addError($this->__('Country id is invalid'));
            $this->_redirect(
                '*/*/hscodeEdit', array(
                    'id'         => $hsCodeId,
                    'active_tab' => 'grid_section'
                )
            );
        }

        try {
            /** @var \OnePica_AvaTax_Model_Records_HsCodeCountry $hsCodeCountryModel */
            $hsCodeCountryModel = Mage::getModel('avatax_records/hsCodeCountry')->load($hsCodeCountryId);

            $hsCodeCountryModel->setId($hsCodeCountryId)->delete();

            $this->getAdminhtmlSession()->addSuccess($this->__('Item was successfully deleted'));

            $this->_redirect(
                '*/*/hscodeEdit', array(
                    'id'         => $hsCodeId,
                    'active_tab' => 'grid_section'
                )
            );
        } catch (Exception $e) {
            $this->getAdminhtmlSession()->addError($e->getMessage());
            $this->_redirect(
                '*/*/hscodecountriesEdit', array(
                    'id'         => $hsCodeCountryId,
                    'hs_code_id' => $hsCodeId
                )
            );
        }
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

        if (!$this->getRequest()->getPost()) {
            $this->getAdminhtmlSession()->addError($this->__('Post data is empty'));
            $this->_redirect(
                '*/*/hscodecountriesEdit', array(
                    'id'         => $hsCodeCountryId,
                    'hs_code_id' => $hsCodeId
                )
            );
        }

        try {
            /** @var \OnePica_AvaTax_Model_Records_HsCodeCountry $hsCodeCountryModel */
            $hsCodeCountryModel = Mage::getModel('avatax_records/hsCodeCountry');
            $countryCodes = $this->_getCountryListAsString($this->getRequest()->getPost('country_codes'));

            $hsCodeCountryModel->setId($hsCodeCountryId)
                               ->setHsId((int)$hsCodeId)
                               ->setHsFullCode((string)$this->getRequest()->getPost('hs_full_code'))
                               ->setCountryCodes($countryCodes)
                               ->save();

            $this->getAdminhtmlSession()->addSuccess($this->__('Item was successfully saved'));
            $this->getAdminhtmlSession()->setHsCodeCountriesData(false);

            $this->_redirect(
                '*/*/hscodeEdit', array(
                    'id'         => $hsCodeId,
                    'active_tab' => 'grid_section'
                )
            );
        } catch (Exception $e) {
            $this->getAdminhtmlSession()->addError($e->getMessage());
            $this->getAdminhtmlSession()->setHsCodeCountriesData($this->getRequest()->getPost());

            $this->_redirect(
                '*/*/hscodecountriesEdit', array(
                    'id'         => $hsCodeCountryId,
                    'hs_code_id' => $hsCodeId
                )
            );
        }
    }

    /**
     * Parameters grid action
     *
     * @return $this
     */
    public function parameterAction()
    {
        $this->_setTitle($this->__('AvaTax'))
             ->_setTitle($this->__('Customs Duty'))
             ->_setTitle($this->__('AvaTax Parameters'));

        $this->loadLayout()
             ->_setActiveMenu('avatax/landedcost/avatax_parameter')
             ->renderLayout();

        return $this;
    }

    /**
     * Parameters new action
     */
    public function parameterNewAction()
    {
        $this->_forward('parameterEdit');
    }

    /**
     * Parameters edit action
     */
    public function parameterEditAction()
    {
        $this->_setTitle($this->__('AvaTax'))
             ->_setTitle($this->__('Customs Duty'))
             ->_setTitle($this->__('AvaTax Parameters'));

        $parameterId = $this->getRequest()->getParam('id');
        /** @var \OnePica_AvaTax_Model_Records_Parameter $parameterModel */
        $parameterModel = Mage::getModel('avatax_records/parameter')->load($parameterId);

        if ($parameterModel->getId() || $parameterId == 0) {
            try {
                Mage::register('parameter_data', $parameterModel);

                $this->loadLayout()->_setActiveMenu('avatax/landedcost/avatax_parameter');

                $this->_addContent(
                    $this->getLayout()->createBlock('avatax/adminhtml_landedcost_parameter_edit')
                );

                $this->renderLayout();
            } catch (Mage_Core_Exception $e) {
                $this->getAdminhtmlSession()->addError($e->getMessage());
                $this->_redirect('*/*/parameter');
            }
        } else {
            $this->getAdminhtmlSession()->addError($this->__('Item does not exist'));

            $this->_redirect('*/*/parameter');
        }
    }

    /**
     * Parameters save action
     *
     * @throws \Varien_Exception
     */
    public function parameterSaveAction()
    {
        $parameterId = $this->getRequest()->getParam('id');

        if (!$this->getRequest()->getPost()) {
            $this->getAdminhtmlSession()->addError($this->__('Post data is empty'));
            $this->_redirect('*/*/parameterEdit', array('id' => $parameterId));
        }

        try {
            /** @var \OnePica_AvaTax_Model_Records_Parameter $parameterModel */
            $parameterModel = Mage::getModel('avatax_records/parameter');

            $countryList = $this->_getCountryListAsString($this->getRequest()->getPost('country_list'));

            $parameterModel->setId($parameterId);
            $parameterModel->setAvalaraUom((string)$this->getRequest()->getPost('avalara_uom'))
                                   ->setAvalaraParameterType((string)$this->getRequest()->getPost('avalara_parameter_type'))
                                   ->setDescription((string)$this->getRequest()->getPost('description'))
                                   ->setCountryList($countryList)
                                   ->save();

            $this->getAdminhtmlSession()->addSuccess($this->__('Item was successfully saved'));
            $this->getAdminhtmlSession()->setParameterData(false);

            $this->_redirectAfterSaveModel($parameterModel, '*/*/parameter');
        } catch (Exception $e) {
            $this->getAdminhtmlSession()->addError($e->getMessage());
            $this->getAdminhtmlSession()->setParameterData($this->getRequest()->getPost());

            $this->_redirect('*/*/parameterEdit', array('id' => $parameterId));
        }
    }

    /**
     * Parameters delete action
     */
    public function parameterDeleteAction()
    {
        $parameterId = $this->getRequest()->getParam('id');

        if ($parameterId <= 0) {
            $this->getAdminhtmlSession()->addError($this->__('Parameter id is invalid'));
            $this->_redirect('*/*/parameter');
        }

        try {
            /** @var \OnePica_AvaTax_Model_Records_Parameter $parameterModel */
            $parameterModel = Mage::getModel('avatax_records/parameter')->load($parameterId);

            $parameterModel->setId($parameterId)->delete();

            $this->getAdminhtmlSession()->addSuccess($this->__('Item was successfully deleted'));

            $this->_redirect('*/*/parameter');
        } catch (Exception $e) {
            $this->getAdminhtmlSession()->addError($e->getMessage());
            $this->_redirect(
                '*/*/hscodecountriesEdit', array(
                    'id' => $parameterId,
                )
            );
        }
    }

    /**
     * Parameters mass delete action
     *
     * @return $this
     */
    public function parameterMassDeleteAction()
    {
        $parameterIds = $this->getRequest()->getParam('parameter');

        if (!is_array($parameterIds)) {
            $this->getAdminhtmlSession()->addError(
                Mage::helper('adminhtml')->__('Please select Parameter(s).')
            );
        } else {
            try {
                /** @var \Mage_Core_Model_Resource_Transaction $transaction */
                $transaction = Mage::getModel('core/resource_transaction');

                /** @var \OnePica_AvaTax_Model_Records_Parameter $parameterModel */
                $parameterModel = Mage::getModel('avatax_records/parameter');

                foreach ($parameterIds as $id) {
                    $parameter = clone $parameterModel;
                    $parameter->load($id);
                    $transaction->addObject($parameter);
                }

                $transaction->delete();

                $this->getAdminhtmlSession()->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($parameterIds))
                );
            } catch (Exception $e) {
                $this->getAdminhtmlSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/parameter');

        return $this;
    }

    /**
     * Agreements grid action
     *
     * @return $this
     */
    public function agreementAction()
    {
        $this->_setTitle($this->__('AvaTax'))
             ->_setTitle($this->__('Customs Duty'))
             ->_setTitle($this->__('AvaTax Agreements'));

        $this->loadLayout()
             ->_setActiveMenu('avatax/landedcost/avatax_agreement')
             ->renderLayout();

        return $this;
    }

    /**
     * Agreements new action
     */
    public function agreementNewAction()
    {
        $this->_forward('agreementEdit');
    }

    /**
     * Agreements edit action
     */
    public function agreementEditAction()
    {
        $this->_setTitle($this->__('AvaTax'))
             ->_setTitle($this->__('Customs Duty'))
             ->_setTitle($this->__('AvaTax Agreements'));

        $agreementId = $this->getRequest()->getParam('id');
        /** @var \OnePica_AvaTax_Model_Records_Agreement $agreementModel */
        $agreementModel = Mage::getModel('avatax_records/agreement')->load($agreementId);

        if ($agreementModel->getId() || $agreementId == 0) {
            try {
                Mage::register('agreement_data', $agreementModel);

                $this->loadLayout()->_setActiveMenu('avatax/landedcost/avatax_agreement');

                $this->_addContent($this->getLayout()->createBlock('avatax/adminhtml_landedcost_agreement_edit'));

                $this->renderLayout();
            } catch (Mage_Core_Exception $e) {
                $this->getAdminhtmlSession()->addError($e->getMessage());
                $this->_redirect('*/*/agreement');
            }
        } else {
            $this->getAdminhtmlSession()->addError($this->__('Item does not exist'));

            $this->_redirect('*/*/agreement');
        }
    }

    /**
     * Agreements save action
     *
     * @throws \Varien_Exception
     */
    public function agreementSaveAction()
    {
        $agreementId = $this->getRequest()->getParam('id');

        if (!$this->getRequest()->getPost()) {
            $this->getAdminhtmlSession()->addError($this->__('Post data is empty'));
            $this->_redirect('*/*/agreementEdit', array('id' => $agreementId));
        }

        try {
            /** @var \OnePica_AvaTax_Model_Records_Agreement $agreementModel */
            $agreementModel = Mage::getModel('avatax_records/agreement');

            $countryList = $this->_getCountryListAsString($this->getRequest()->getPost('country_list'));

            $agreementModel->setId($agreementId);
            $agreementModel->setAvalaraAgreementCode((string)$this->getRequest()->getPost('avalara_agreement_code'))
                           ->setDescription((string)$this->getRequest()->getPost('description'))
                           ->setCountryList($countryList)
                           ->save();

            $this->getAdminhtmlSession()->addSuccess($this->__('Item was successfully saved'));
            $this->getAdminhtmlSession()->setAgreementData(false);

            $this->_redirectAfterSaveModel($agreementModel, '*/*/agreement');
        } catch (Exception $e) {
            $this->getAdminhtmlSession()->addError($e->getMessage());
            $this->getAdminhtmlSession()->setAgreementData($this->getRequest()->getPost());

            $this->_redirect('*/*/agreementEdit', array('id' => $agreementId));
        }
    }

    /**
     * Agreement delete action
     */
    public function agreementDeleteAction()
    {
        $agreementId = $this->getRequest()->getParam('id');

        if ($agreementId <= 0) {
            $this->getAdminhtmlSession()->addError($this->__('Agreement id is invalid'));
            $this->_redirect('*/*/agreement');
        }

        try {
            /** @var \OnePica_AvaTax_Model_Records_Agreement $agreementModel */
            $agreementModel = Mage::getModel('avatax_records/agreement')->load($agreementId);

            $agreementModel->setId($agreementId)->delete();

            $this->getAdminhtmlSession()->addSuccess($this->__('Item was successfully deleted'));

            $this->_redirect('*/*/agreement');
        } catch (Exception $e) {
            $this->getAdminhtmlSession()->addError($e->getMessage());
            $this->_redirect(
                '*/*/agreementEdit', array(
                    'id' => $agreementId,
                )
            );
        }
    }

    /**
     * Agreement mass delete action
     *
     * @return $this
     */
    public function agreementMassDeleteAction()
    {
        $agreementIds = $this->getRequest()->getParam('agreements');

        if (!is_array($agreementIds)) {
            $this->getAdminhtmlSession()->addError(Mage::helper('adminhtml')->__('Please select  Agreement(s).'));
        } else {
            try {
                /** @var \Mage_Core_Model_Resource_Transaction $transaction */
                $transaction = Mage::getModel('core/resource_transaction');

                /** @var \OnePica_AvaTax_Model_Records_Agreement $agreementModel */
                $agreementModel = Mage::getModel('avatax_records/agreement');

                foreach ($agreementIds as $agreementId) {
                    $agreement = clone $agreementModel;
                    $transaction->addObject($agreement->load($agreementId));
                }

                $transaction->delete();

                $this->getAdminhtmlSession()->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($agreementIds))
                );
            } catch (Exception $e) {
                $this->getAdminhtmlSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/agreement');

        return $this;
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

    /**
     * @param array $array
     * @return string
     */
    protected function _getCountryListAsString($array)
    {
        return is_array($array) ? implode(',', array_filter($array)) : (string)$array;
    }

    /**
     * @param Mage_Core_Model_Abstract $model
     * @param string                   $action
     */
    protected function _redirectAfterSaveModel($model, $action)
    {
        if ($this->getRequest()->getParam('back')) {
            $this->_redirect(
                '*/*/' . $this->getRequest()->getParam('back'),
                array(
                    'id' => $model->getId()
                )
            );
        } else {
            $this->_redirect($action);
        }
    }

    /**
     * @return \Mage_Adminhtml_Model_Session
     */
    protected function getAdminhtmlSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
}
