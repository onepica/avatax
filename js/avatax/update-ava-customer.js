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

var AvaTaxCertCustomerForm = Class.create();
AvaTaxCertCustomerForm.init = function (actionUrl, idCountries, idRegions, idRegionAsText) {
    this.actionUrl = actionUrl;
    this.ctrlCountries = $(idCountries);
    this.ctrlRegions = $(idRegions);
    this.ctrlRegionAsText = $(idRegionAsText);

    if( $$("label[for='" + idRegions + "']").length > 0) {
        $$("label[for='" + idRegions + "']")[0].insert('<span class="required"> *</span>');
    }

    if(this.ctrlRegions.options.length > 0) {
        $(this.ctrlRegionAsText).hide();
    } else {
        $(this.ctrlRegions).hide();
    }
};

AvaTaxCertCustomerForm.onCountryChanged = function () {
    var countryCode = this.ctrlCountries.value;
    new Ajax.Request(this.actionUrl, {
        method: "POST",
        parameters: {
            parent: countryCode
        },
        requestHeaders: {Accept: "application/json"},
        onSuccess: function (transport) {
            try {
                if (transport.responseText) {
                    var options = transport.responseText.evalJSON(true);
                    // Clear the old options
                    this.ctrlRegions.options.length = 0;
                    this.ctrlRegionAsText.value = null;
                    // /////////////////////

                    // Load the new options
                    if(options.length > 0) {
                        $(this.ctrlRegionAsText).hide();
                        $(this.ctrlRegions).show();

                        for (var index = 0; index < options.length; ++index) {
                            var option = options[index];
                            this.ctrlRegions.options.add(new Option(option.label, option.value));
                        }
                    } else {
                        $(this.ctrlRegions).hide();
                        $(this.ctrlRegionAsText).show();
                    }
                    // /////////////////////
                }
            } catch (e) {
                console.log(e);
            }
        }.bind(this),
        onFailure: function (transport) {
            if (transport.responseText) {
                var response = transport.responseText.evalJSON(true);
                console.log(response.message);
            } else {
                console.log("The response is empty");
            }
        }.bind(this)
    });
}
