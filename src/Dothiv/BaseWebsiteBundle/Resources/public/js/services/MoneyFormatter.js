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
     * @param c the number to format
     * @param t the thousands separator
     * @returns {string}
     */
    MoneyFormatter.prototype.formatDecimalNumber = function (c, t) {
        var i = parseInt(c, 10) + "";
        var j = (j = i.length) > 3 ? j % 3 : 0;
        return (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t);
    };

    MoneyFormatter.prototype.decimalFormat = function (value, currencySymbol) {
        switch (this.locale) {
            case 'de':
                return this.formatDecimalNumber(value, '.') + ' ' + currencySymbol;
            default:
                return currencySymbol + this.formatDecimalNumber(value, ',');
        }
    };

    /**
     * Formats a float money value.
     * Strips tailing decimal separator followed by zeros
     */
    MoneyFormatter.prototype.format = function (value, currencySymbol) {
        var f = Math.round((value - Math.floor(value)) * 100);
        var add = 0;
        if (f === 100) {
            f = 0;
            add = 1;
        }
        var frac = f < 10 ? "0" + f : "" + f;
        switch (this.locale) {
            case 'de':
                return this.formatDecimalNumber(parseInt(value, 10) + add, '.') + (frac != '00' ? ',' + frac : '') + ' ' + currencySymbol;
            default:
                return currencySymbol + this.formatDecimalNumber(parseInt(value, 10) + add, ',') + (frac != '00' ? '.' + frac : '');
        }
    };

    return MoneyFormatter;

}]);
