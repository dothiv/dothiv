'use strict';

/**
 * Service for converting IDN domains to ASCII.
 */
angular.module('dotHIVApp.services').factory('idn', [function () {
    function _toASCII(domain) {
        var name = domain.replace(/\.hiv$/, '');
        var encoded = punycode.encode(name);
        if (encoded == name + "-") {
            // https://rt.cpan.org/Public/Bug/Display.html?id=94347
            return domain;
        }
        return "xn--" + encoded + ".hiv";
    }

    return {
        toASCII: _toASCII
    }
}]);
