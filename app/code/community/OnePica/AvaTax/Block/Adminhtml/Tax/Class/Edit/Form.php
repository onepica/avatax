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


class OnePica_AvaTax_Block_Adminhtml_Tax_Class_Edit_Form extends Mage_Adminhtml_Block_Tax_Class_Edit_Form
{
	
    protected function _prepareForm()
    {
    	parent::_prepareForm();
    	$fieldset = $this->getForm()->getElement('base_fieldset');
        
    	$model  = Mage::registry('tax_class');
        $fieldset->addField('op_avatax_code', 'text',
            array(
                'name'  => 'op_avatax_code',
                'label' => Mage::helper('avatax')->__('AvaTax Code'),
                'value' => $model->getOpAvataxCode(),
            )
        );

        return $this;
    }
}