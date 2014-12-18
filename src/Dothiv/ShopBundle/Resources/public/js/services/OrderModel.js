'use strict';

angular.module('dotHIVApp.services').factory('OrderModel', [function () {
    var OrderModel = function () {
        this.clickcounter = true;
        this.redirect = "";
        this.duration = 1;
        this.contact = {
            email: "",
            organization: "",
            country: null
        };
        this.stripe = {
            token: "",
            card: ""
        };
        this.countryModel = null;
    };

    OrderModel.prototype.isDone = function () {
        if (!this.stripe.token.length) {
            return false;
        }
        if (!this.stripe.card.length) {
            return false;
        }
        return true;
    };

    OrderModel.prototype.isConfigured = function () {
        if (!this.redirect.length) {
            return false;
        }
        return true;
    };

    OrderModel.prototype.vatIncluded = function () {
        if (this.countryModel == null) {
            return false;
        }
        if (!this.countryModel.eu) {
            return false;
        }
        if (!this.contact.organization || this.contact.organization.length == 0) {
            return false;
        }
        return true;
    };

    OrderModel.prototype.flatten = function () {
        return {
            "clickcounter": this.clickcounter, // 1
            "redirect": this.redirect, // http://jana.com/
            "duration": this.duration, // 3
            "firstname": this.contact.firstname, // Jana
            "lastname": this.contact.lastname, // Bürger
            "email": this.contact.email, // jana.müller@bürger.de
            "phone": this.contact.phone, // +49301234567
            "fax": this.contact.fax, // +4930123456777
            "locality": this.contact.locality, // Waldweg 1
            "locality2": this.contact.locality2, // Hinterhaus
            "city": this.contact.city, // 12345 Neustadt
            "country": this.contact.country, // Germany (Deutschland)
            "organization": this.contact.organization, // Bürger GmbH
            "vatNo": this.contact.vat, // DE123456789
            "stripeToken": this.stripe.token, // tok_14kvt242KFPpMZB00CUopZjt
            "stripeCard": this.stripe.card // crd_14kvt242KFPpMZB00CUopZjt
        };
    };

    return new OrderModel();
}]);
