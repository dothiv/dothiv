'use strict';

/**
 * Service for getting VAT rules to apply according to https://trello.com/c/1fOKtchM/9-vat-rules
 */
angular.module('dotHIVApp.services').factory('VatRules', ['config', function (config) {

    /**
     * @param {boolean} isOrg
     * @param {object} country
     * @param {boolean} hasVatNo
     * @constructor
     */
    function VatRules(isOrg, country, hasVatNo) {
        this.country = country;
        this.isOrg = !!isOrg;
        this.hasVatNo = !!hasVatNo;
        this.vat = config.vat.de;
    }

    /**
     * @returns {boolean}
     */
    VatRules.prototype.isEuCountry = function () {
        return this.country && this.country.eu;
    };

    /**
     * @returns {boolean}
     */
    VatRules.prototype.isOrganization = function () {
        return this.isOrg;
    };

    /**
     * @returns {boolean}
     */
    VatRules.prototype.isGermany = function () {
        return this.country && this.country.iso === "DE";
    };

    /**
     * @returns {boolean}
     */
    VatRules.prototype.hasVatNumber = function () {
        return this.hasVatNo;
    };

    /**
     * @returns {boolean}
     */
    VatRules.prototype.vatNoEnabled = function () {
        if (this.isOrganization() && this.isEuCountry()) {
            return true;
        }
        return false;
    };

    /**
     * @returns {boolean}
     */
    VatRules.prototype.vatNoRequired = function () {
        if (this.isOrganization() && this.isGermany()) {
            return true;
        }
        return false;
    };

    /**
     * @returns {boolean}
     */
    VatRules.prototype.showReverseChargeNote = function () {
        if (this.isOrganization()
            && this.isEuCountry() && !this.isGermany()
            && this.hasVatNumber()
        ) {
            return true;
        }
        return false;
    };

    /**
     * @returns {int}
     */
    VatRules.prototype.getVat = function () {
        if (!this.isOrganization()) {
            return this.vat;
        }
        if (this.isGermany()) {
            return this.vat;
        }
        if (this.isEuCountry() && !this.hasVatNumber()) {
            return this.vat;
        }
        return 0;
    };

    return VatRules;

}]);
