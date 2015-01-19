'use strict';

angular.module('dotHIVApp.services').factory('Price', ['config', 'MoneyFormatter', function (config, MoneyFormatter) {
    var Price = function () {
        this.mf = new MoneyFormatter(config.locale);
        this.promoMatch = /.+4life\.hiv$/;
        this.vat = config.vat.de;
        this.symbols = {'usd': '$', 'eur': '€'};
    };

    Price.prototype.getPricePerYear = function (domain, currency) {
        currency = currency.toLowerCase();
        var pricePerYear = config.shop.price[currency];
        if (config.shop.promo.name4life && domain.match(this.promoMatch)) {
            pricePerYear += config.shop.promo.name4life[currency];
        }
        return pricePerYear;
    };

    Price.prototype.getPricePerMonth = function (domain, currency) {
        return Math.round(this.getPricePerYear(domain, currency) / 12);
    };

    Price.prototype.getFormattedPricePerYear = function (domain, currency) {
        return this.mf.format(this.getPricePerYear(domain, currency) / 100, this.getSymbol(currency));
    };

    Price.prototype.getFormattedPricePerMonth = function (domain, currency) {
        return this.mf.format(this.getPricePerMonth(domain, currency) / 100, this.getSymbol(currency));
    };

    Price.prototype.calculateVat = function (price) {
        return Math.round(price * (this.vat / 100));
    };

    Price.prototype.getVat = function () {
        return this.vat;
    };

    Price.prototype.format = function (price, currency) {
        return this.mf.format(price, this.getSymbol(currency));
    };

    Price.prototype.getSymbol = function (currency) {
        return this.symbols[currency.toLowerCase()];
    };

    return new Price();
}]);
