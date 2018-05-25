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

var AvaTax = Class.create({});
AvaTax._config = {
    updateCompaniesSelect: function (url) {
        this.valueUrl = $("tax_avatax_url").value;
        this.valueAccount = $("tax_avatax_account").value;
        this.valueLicense = $("tax_avatax_license").value;
        var required = ["tax_avatax_license", "tax_avatax_account"];
        var canMakeCall = true;

        // check required fields
        required.each(function (elmtId) {
            var elm = $(elmtId);
            var advice = Validation.getAdvice("required-entry", elm);
            if (advice === null) {
                advice = Validation.createAdvice("required-entry", elm);
            }

            if (!elm.value) {
                Validation.showAdvice(elm, advice, "required-entry");
                canMakeCall = false;
            } else {
                Validation.hideAdvice(elm, advice);
            }
        });

        if (!canMakeCall) {
            return;
        }

        new Ajax.Request(url, {
            method: "POST",
            parameters: {
                url: this.valueUrl,
                account: this.valueAccount,
                license: this.valueLicense
            },
            requestHeaders: {Accept: "application/json"},
            onSuccess: function (transport) {
                try {
                    if (transport.responseText) {
                        var response = transport.responseText.evalJSON(true);

                        var select = $("tax_avatax_company_code");
                        select.options.length = 0;

                        response.companies.each(function (element) {
                            select.insert(new Element(
                                "option", {value: element.company_code}
                            ).update(element.company_name + " (" + element.company_code + ")"));
                        });
                        AvaTax._general.removeMessages();
                        if (response.success) {
                            AvaTax._general.showMessage(response.message, "success");
                        } else {
                            AvaTax._general.showMessage(response.message, "error");
                        }
                    }
                } catch (e) {
                    console.log(e);
                }
            }.bind(this)
        });
    }
};

AvaTax._general = {
    showMessage: function (txt, type) {
        var html = '<ul class="messages"><li class="' + type + '-msg"><ul><li>' + txt + '</li></ul></li></ul>';
        $("messages").update(html);
    },
    removeMessages: function () {
        $("messages").update("");
    }
};
