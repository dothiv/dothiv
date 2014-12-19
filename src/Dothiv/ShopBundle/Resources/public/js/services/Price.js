'use strict';

angular.module('dotHIVApp.services').factory('Price', ['config', 'MoneyFormatter', function (config, MoneyFormatter) {
    var Price = function () {
        this.mf = new MoneyFormatter(config.locale);
        this.currency = config.locale == 'en' ? 'usd' : 'eur';
        this.symbol = config.locale == 'en' ? '$' : '€';
        this.regularPrice = config.shop.price[this.currency];
        this.promoModPrice = config.shop.promo.name4life[this.currency];
        this.promoMatch = /.+4life\.hiv$/;
        this.vat = config.vat.de;
    };

    Price.prototype.getPricePerYear = function (domain) {
        var pricePerYear = this.regularPrice;
        if (domain.match(this.promoMatch)) {
            pricePerYear += this.promoModPrice;
        }
        return pricePerYear;
    };

    Price.prototype.getPricePerMonth = function (domain) {
        return Math.round(this.getPricePerYear(domain) / 12);
    };

    Price.prototype.getFormattedPricePerYear = function (domain) {
        return this.mf.format(this.getPricePerYear(domain) / 100, this.symbol);
    };

    Price.prototype.getFormattedPricePerMonth = function (domain) {
        return this.mf.format(this.getPricePerMonth(domain) / 100, this.symbol);
    };

    Price.prototype.calculateVat = function (price) {
        return Math.round(price * (this.vat / 100));
    };

    Price.prototype.format = function (price) {
        return this.mf.format(price, this.symbol);
    };

    return new Price();
}]);
