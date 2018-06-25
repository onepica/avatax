var AvaTaxCert = Class.create();

AvaTaxCert.initApi = function (token, ship_zone) {
    if (GenCert.getStatus() != 0) {
        GenCert.hide();
    }

    var form_element = document.getElementById("avatax_certcapture_form_parent");
    form_element.className = "";
    GenCert.init(form_element, {
        token: token,
        ship_zone: ship_zone,

        onCertSuccess: function () {
            alert("Certificate id created successfully: " + GenCert.certificateIds);
        },
    });

    GenCert.show();

    document.getElementById("avatax_certcapture_gencert2_button").style.display = "none";
};

AvaTaxCert.showPopup = function (url, title) {
    win = new Window({
        title: title,
        url: url,
        id:"avatax_certcapture",
        zIndex: 3000,
        destroyOnClose: true,
        recenterAuto: false,
        resizable: false,
        width: 450,
        height: 473,
        minimizable: false,
        maximizable: false,
        draggable: false
    });
    win.showCenter(true);
};
