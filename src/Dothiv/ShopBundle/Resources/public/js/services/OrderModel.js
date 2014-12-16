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

    return new OrderModel();
}]);
