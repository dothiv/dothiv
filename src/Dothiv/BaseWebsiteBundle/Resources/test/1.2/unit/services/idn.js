'use strict';

describe('punycode server', function () {

    beforeEach(module('dotHIVApp.services'));

    it('it should convert a IDN domain', inject(function (idn) {
        expect(idn.toASCII('über.hiv')).toEqual('xn--ber-goa.hiv');
    }));

    it('it should not convert a ASCII domain', inject(function (idn) {
        expect(idn.toASCII('uber.hiv')).toEqual('uber.hiv');
    }));

});
