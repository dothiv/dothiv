'use strict';

/**
 * Service for formatting money according to the current locale.
 */
angular.module('dotHIVApp.services').factory('MoneyFormatter', [function () {

    function MoneyFormatter(locale) {
        this.locale = locale;
    }

    /**
     * @see http://stackoverflow.com/a/149099
     * @param c
     * @param d
     * @param t
     * @returns {string}
     */
    MoneyFormatter.prototype.formatMoney = function (c, d, t) {
        var i = parseInt(c, 10) + "";
        var j = (j = i.length) > 3 ? j % 3 : 0;
        return (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t);
    };

    MoneyFormatter.prototype.decimalFormat = function (value, currencySymbol) {
        switch (this.locale) {
            case 'de':
                return this.formatMoney(value, '', '.') + ' ' + currencySymbol;
            default:
                return currencySymbol + this.formatMoney(value, '', ',');
        }
    };

    return MoneyFormatter;

}]);
