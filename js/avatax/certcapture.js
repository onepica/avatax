var AvaTaxCert = Class.create();

AvaTaxCert.prototype = {
    initApi: function (token, ship_zone) {
        if (GenCert.getStatus() != 0) {
            GenCert.hide();
        }

        var form_element = document.getElementById('form_parent');
        form_element.className = "";
        GenCert.init(form_element, {
            token: token,
            ship_zone: ship_zone,

            onCertSuccess: function () {
                alert("Certificate id created successfully: " + GenCert.certificateIds);
            },
        });

        GenCert.show();

        document.getElementById('gencert2_button').style.display = 'none';
    }
}
