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

        this.valueUrl = $('tax_avatax_url').value;
        this.valueAccount = $('tax_avatax_account').value;
        this.valueLicense = $('tax_avatax_license').value;

        var params = {
            url: this.valueUrl,
            account: this.valueAccount,
            license: this.valueLicense
        };

        new Ajax.Request(url, {
            method: 'POST',
            parameters: params,
            requestHeaders: {Accept: 'application/json'},
            onSuccess: function (transport) {
                try {
                    if (transport.responseText) {
                        var response = transport.responseText.evalJSON(true);

                        debugger;
                    }
                } catch (e) {
                    console.log(e);
                    debugger;
                }
            }.bind(this)
        });
    }
};
