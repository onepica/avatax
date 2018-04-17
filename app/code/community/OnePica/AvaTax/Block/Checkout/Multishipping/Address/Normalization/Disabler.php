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
class OnePica_AvaTax_Block_Checkout_Multishipping_Address_Normalization_Disabler
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

        $html = "<p>
            <input type='checkbox'
                    name='allow_normalize_shipping_address'
                    id='allow_normalize_shipping_address'
                    value='1'
                    class='checkbox'
                    onclick='window.avataxReloadShippingMethods();'
                    " . $checked . ">
            <label for='allow_normalize_shipping_address'>$label</label>
             <span class='please-wait allow-normalize' id='allow-normalize-please-wait' style='display: none;'>
                <img src='$loaderImgUrl' alt='$loaderLabel' title='$loaderLabel' class='v-middle'>
                $loaderLabel
            </span>
            <script type='application/javascript'>
                window.avataxReloadShippingMethods = function() {
                    var isChecked = 0;
                    if ($('allow_normalize_shipping_address').checked){
                        isChecked = 1;
                    }

                    $$('form button').each(function (elem) {
                        elem.addClassName('disabled');
                    });

                    $('allow_normalize_shipping_address').setAttribute('disabled', 'disabled');
                    $('allow-normalize-please-wait').setStyle({
                         'display': 'block'
                    });

                    var request = new Ajax.Request(
                        '/avatax/normalization/update',
                        {
                            method:'post',
                            parameters:{flag:isChecked,multishipping:1},
                            onSuccess: function(response){
                                window.location.href = window.location.href;
                            }
                        }
                    );
                };
            </script>
        </p>";

        return $html;
    }
}
