'use strict';

/**
 * Service for converting IDN domains to ASCII (and vice versa).
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

    function _toUnicode(domain) {
        if (domain.substr(0, 4) !== "xn--") {
            // Not a punycode domain
            return domain;
        }
        return punycode.decode(domain.substr(4).replace(/\.hiv$/, '')) + ".hiv";
    }

    return {
        toASCII: _toASCII,
        toUnicode: _toUnicode
    }
}]);
