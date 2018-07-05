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
document.write("<script src='https://app.certcapture.com/gencert2/js'><\/script>");

var AvaTaxCert = Class.create();

AvaTaxCert.initApi = function (tokenUrl) {
    if (GenCert.getStatus() != 0) {
        GenCert.hide();
    }
    var ship_zone = $("ship_zone").value,
        customer_number = $("customer_number").value,
        form_element = $("avatax_certcapture_form_parent");

    form_element.className = "";

    new Ajax.Request(tokenUrl, {
        method: "POST",
        parameters: {
            url: tokenUrl,
            customerNumber: customer_number
        },
        requestHeaders: {Accept: "application/json"},
        onSuccess: function (transport) {
            try {
                if (transport.responseText) {
                    var response = transport.responseText.evalJSON(true),
                        form = $("avatax_certcapture_form_container");
                    if (response.success) {
                        GenCert.init(form_element, {
                            token: response.token,
                            ship_zone: ship_zone,
                            onCertSuccess: function () {
                                var closeButton = $("avatax_certcapture_form_close"),
                                    closeButtonText = closeButton.readAttribute("data-close-text"),
                                    message = "Certificate id created successfully: " + GenCert.certificateIds;
                                closeButton.update("<span>" + closeButtonText + "</span>");
                                console.log(message);
                            },
                        });

                        GenCert.show();
                        $("avatax_certcapture_form_submit").hide();

                        form.select("input").forEach(function (item, i) {
                            item.disable();
                        });
                    } else {
                        console.log(response.message);
                    }
                }
            } catch (e) {
                console.log(e);
            }
        }.bind(this)
    });
};

AvaTaxCert.showPopup = function (url, title) {
    win = new Window({
        title: title,
        id: "avatax_certcapture",
        zIndex: 3000,
        destroyOnClose: true,
        recenterAuto: false,
        resizable: false,
        width: 780,
        height: 540,
        minimizable: false,
        maximizable: false,
        draggable: false
    });
    win.showCenter(true);
    win.setAjaxContent(url);
};

AvaTaxCert.delete = function (certId, customerId, url, jsObject) {
    new Ajax.Request(url, {
        method: "POST",
        parameters: {
            certId: certId,
            customerId: customerId
        },
        requestHeaders: {Accept: "application/json"},
        onSuccess: function (transport) {
            try {
                if (transport.responseText) {
                    var response = transport.responseText.evalJSON(true);

                    AvaTax._general.removeMessages();
                    if (response.success) {
                        AvaTax._general.showMessage(response.message, "success");
                    } else {
                        AvaTax._general.showMessage(response.message, "error");
                    }
                    jsObject.doFilter();
                }
            } catch (e) {
                console.log(e);
            }
        }.bind(this)
    });
};
