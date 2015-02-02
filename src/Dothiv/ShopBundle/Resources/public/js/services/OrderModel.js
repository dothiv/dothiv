'use strict';

angular.module('dotHIVApp.services').factory('OrderModel', [function () {
    var OrderModel = function () {
        this.init();
    };

    OrderModel.prototype.init = function () {
        this.domain = "";
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
        this.defaultCurrency = 'USD';
        this.currency = this.defaultCurrency;
        this.available = false;
        this.language = 'en';
        this.gift = false;
        this.presentee = {
            firstname: "",
            lastname: "",
            email: ""
        };
        this.landingpage = {
            owner: ""
        };
        this.step = 1;
    };

    OrderModel.prototype.setDomain = function (domain) {
        if (this.domain !== domain) {
            this.init();
            this.domain = domain;
        }
    };

    OrderModel.prototype.getDomain = function () {
        return this.domain;
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

    OrderModel.prototype.isAvailable = function () {
        return this.available;
    };

    OrderModel.prototype.is4lifeDomain = function () {
        return this.domain.match(/4life.hiv$/) != null;
    };

    OrderModel.prototype.isConfigured = function () {
        if (this.is4lifeDomain()) {
            if (this.gift) {
                if (!this.presentee.firstname.length) {
                    return false;
                }
                if (!this.presentee.lastname.length) {
                    return false;
                }
                if (!this.presentee.email.length) {
                    return false;
                }
            }
        } else {
            if (!this.redirect.length) {
                return false;
            }
        }
        return true;
    };

    OrderModel.prototype.vatIncluded = function () {
        if (this.countryModel == null) {
            return false;
        }
        if (this.countryModel.iso === 'DE') {
            // Germans always pay VAT
            return true;
        }
        if (!this.countryModel.eu) {
            // Out of EU
            // private -> vat
            if (!this.contact.isOrg || this.contact.isOrg.length == 0) {
                return false;
            }
            // organization  -> no vat
            return false;
        }
        // In eu
        if (!this.contact.isOrg || this.contact.isOrg.length == 0) {
            // Private person must pay VAT
            return true;
        }
        if (!this.contact.vat || this.contact.vat.length == 0) {
            // In EU, no VAT number provided -> must pay VAT
            return true;
        }
        return false;
    };

    OrderModel.prototype.flatten = function () {
        var flat = {
            "clickcounter": this.clickcounter, // 1
            "duration": this.duration, // 3
            "firstname": this.contact.firstname, // Jana
            "lastname": this.contact.lastname, // Bürger
            "email": this.contact.email, // jana.müller@bürger.de
            "phone": this.contact.phone, // +49301234567
            "fax": this.contact.fax, // +4930123456777
            "locality": this.contact.locality, // Waldweg 1
            "locality2": this.contact.locality2, // Hinterhaus
            "city": this.contact.city, // 12345 Neustadt
            "country": this.countryModel.iso, // DE
            "organization": this.contact.isOrg, // Bürger GmbH
            "vatNo": this.contact.vat, // DE123456789
            "currency": this.currency, // EUR
            "stripeToken": this.stripe.token, // tok_14kvt242KFPpMZB00CUopZjt
            "stripeCard": this.stripe.card, // crd_14kvt242KFPpMZB00CUopZjt
            "gift": this.gift, // 1
            "language": this.language // en
        };
        if (this.redirect.length) {
            flat.redirect = this.redirect; // http://jana.com/
        }
        if (this.gift) {
            flat.presenteeFirstname = this.presentee.firstname; // Jane
            flat.presenteeLastname = this.presentee.lastname; // Doe
            flat.presenteeEmail = this.presentee.email; // jane.doe@example.de
        }
        if (this.landingpage.owner) {
            flat.landingpageOwner = this.landingpage.owner; // Donald Duck
        }
        return flat;
    };

    return new OrderModel();
}]);
