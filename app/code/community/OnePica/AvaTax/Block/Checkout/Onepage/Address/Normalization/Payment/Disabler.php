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
 * The Onepage Address Normalization Disabler block
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Checkout_Onepage_Address_Normalization_Payment_Disabler
    extends OnePica_AvaTax_Block_Checkout_Abstract
{
    /**
     * Generate checkbox for address normalization disabler
     *
     * @return string
     */
    protected function _toHtml()
    {
        $checked = $this->getQuote()->getAvataxNormalizationFlag() ? "checked='checked'" : '';

        $storeId = $this->getQuote()->getStoreId();
        $label = $this->_getConfigData()->getNormalizeAddressDisablerLabel($storeId);
        $loaderLabel = $this->_getConfigData()->getNormalizeAddressDisablerPleaseWaitLabel($storeId);
        $loaderImgUrl = $this->getSkinUrl('images/opc-ajax-loader.gif');
        $html
            = "<p><!--virtual -->
            <input type='checkbox'
                    name='allow_normalize_shipping_address'
                    id='allow_normalize_shipping_address'
                    value='1'
                    class='checkbox'
                    onclick='checkout.avataxReloadShippingMethodsAccordingNormalization();'
                    " . $checked . ">
            <label for='allow_normalize_shipping_address'>$label</label>
            <span class='please-wait allow-normalize' id='allow-normalize-please-wait' style='display: none;'>
                <img src='$loaderImgUrl' alt='$loaderLabel' title='$loaderLabel' class='v-middle'>
                $loaderLabel
            </span>
            <style type='text/css'>
                .allow-normalize {
                    margin-top: -1px;
                    height: 22px;
                }
            </style>
            <script type='application/javascript'>
                //debugger;
                Checkout.prototype.avataxEnableContinue = function(step, isEnabled){
                    var container = $(step+'-buttons-container');
                    if(isEnabled){
                        container.removeClassName('disabled');
                        container.setStyle({opacity:1});
                    }
                    else {
                        container.addClassName('disabled');
                        container.setStyle({opacity:.5});
                    }
                    this._disableEnableAll(container, !isEnabled);
                };

                Checkout.prototype.avataxIsNormalizationAllowed = function() {
                    var isChecked = 0;
                    var allowNormilize = $('allow_normalize_shipping_address');
                    if (allowNormilize && allowNormilize.checked){
                        isChecked = 1;
                    }

                    return isChecked;
                };

                Checkout.prototype.avataxResetBillingAndShippingProgress = function() {
                    if (this.resetPreviousSteps != undefined && this.resetPreviousSteps != null) {
                        //for magento version >= 1.8.0.0
                        var step = this.currentStep;
                        this.currentStep = 'billing';
                        this.resetPreviousSteps();
                        this.currentStep = step;
                    }
                };

                Checkout.prototype.avataxUpdateProgress = function() {
                    if (this.reloadStep != undefined && this.reloadStep != null) {
                        //for magento version >= 1.8.0.0
                        this.reloadStep('billing');
                        this.reloadStep('shipping');
                    } else if(this.reloadProgressBlock != undefined && this.reloadProgressBlock != null) {
                        //for magento version lower 1.8.0.0
                        this.reloadProgressBlock();
                    }
                };

                Checkout.prototype.avataxSetNormalizationPleaseWait = function() {
                    $('allow_normalize_shipping_address').setAttribute('disabled', 'disabled');

                    if ($('allow-normalize-please-wait')) {
                        $('allow-normalize-please-wait').show();
                    }
                };

                Checkout.prototype.avataxSetNormalizationEnabled = function(){
                    $('allow_normalize_shipping_address').removeAttribute('disabled');
                };

                Checkout.prototype.avataxReloadShippingMethodsAccordingNormalization = function() {
                    //debugger;
                    this.avataxSetNormalizationPleaseWait();
                    this.avataxEnableContinue('payment', false);

                    var isChecked = this.avataxIsNormalizationAllowed();

                    var request = new Ajax.Request(
                        '/avatax/normalization/update',
                        {
                            method:'post',
                            parameters:{flag:isChecked},
                            onSuccess: function(response){
                                checkout.avataxResetBillingAndShippingProgress();

                                //wrap method
                                Checkout.prototype.setStepResponse = Checkout.prototype.setStepResponse.wrap(function(parentMethod, response){

                                    var section = response.goto_section;
                                    switch(section) {
                                        case 'payment': {
                                                parentMethod(response);

                                                //unwrap method
                                                Checkout.prototype.setStepResponse = parentMethod;

                                                this.avataxUpdateProgress();
                                                this.avataxEnableContinue('payment', true);
                                            }
                                            break;
                                    }
                                });
                                billing.save();
                            }
                        }
                    );
                };

                checkout.avataxSetNormalizationEnabled();
            </script>
        </p>";

        return $html;
    }
}
