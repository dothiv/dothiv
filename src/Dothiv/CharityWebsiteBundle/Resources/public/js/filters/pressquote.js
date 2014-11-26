'use strict';

/**
 * Filters PressQuote content element
 */
angular.module('dotHIVApp.filters').filter('pressquote', function () {
    return function (input, config) {
        config = typeof config !== 'undefined' ? config : {};
        var withLogo = typeof config.logo !== 'undefined' ? config.logo : null;
        var withQuote = typeof config.quote !== 'undefined' ? config.quote : null;
        return input.filter(function (pressQuote) {
            if (!pressQuote.show) {
                return false;
            }
            if (withLogo !== null) {
                if (withLogo) {
                    if (typeof pressQuote.logo == 'undefined') {
                        return false;
                    }
                } else {
                    if (typeof pressQuote.logo != 'undefined') {
                        return false;
                    }
                }
            }
            if (withQuote !== null) {
                if (withQuote) {
                    if (typeof pressQuote.quote == 'undefined') {
                        return false;
                    }
                } else {
                    if (typeof pressQuote.quote != 'undefined') {
                        return false;
                    }
                }
            }
            return true;
        });
    }
});
