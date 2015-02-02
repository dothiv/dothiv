'use strict';

function getOrderTestData() {
    var deCountry = {iso: "DE", eu: true};
    var euCountry = {iso: "LI", eu: true};
    var noneuCountry = {iso: "LU", eu: false};
    var hasVatNo = true;
    var isOrg = true;
    return [
        // isOrg, country, hasVatNo, expectedVatNoEnabled, expectedVatNoRequired, expectedShowReverseChargeNote, expectedVAT
        // Private person
        [!isOrg, deCountry, !hasVatNo, false, false, false, 19],
        [!isOrg, euCountry, !hasVatNo, false, false, false, 19],
        [!isOrg, noneuCountry, !hasVatNo, false, false, false, 19],
        [!isOrg, deCountry, hasVatNo, false, false, false, 19],
        [!isOrg, euCountry, hasVatNo, false, false, false, 19],
        [!isOrg, noneuCountry, hasVatNo, false, false, false, 19],
        // Organization …
        // … in Germany
        [isOrg, deCountry, !hasVatNo, true, true, false, 19],
        [isOrg, deCountry, hasVatNo, true, true, false, 19],
        // … in EU
        [isOrg, euCountry, !hasVatNo, true, false, false, 19],
        [isOrg, euCountry, hasVatNo, true, false, true, 0],
        // … outside EU
        [isOrg, noneuCountry, !hasVatNo, false, false, false, 0],
        [isOrg, noneuCountry, hasVatNo, false, false, false, 0]
    ];
}

describe('VatRules service', function () {

    beforeEach(module('dotHIVApp.services'));

    beforeEach(module(function ($provide) {
        $provide.value('config', {
            vat: {
                de: 19
            }
        });
    }));

    it('it should implement the correct rules', inject(function (VatRules) {
        var d = getOrderTestData();
        for (var k in d) {
            var isOrg = d[k][0];
            var country = d[k][1];
            var hasVatNo = d[k][2];
            var expectedVatNoEnabled = d[k][3];
            var expectedVatNoRequired = d[k][4];
            var expectedShowReverseChargeNote = d[k][5];
            var expectedVAT = d[k][6];
            var f = new VatRules(isOrg, country, hasVatNo);
            expect(f.vatNoEnabled()).toEqual(expectedVatNoEnabled);
            expect(f.vatNoRequired()).toEqual(expectedVatNoRequired);
            expect(f.showReverseChargeNote()).toEqual(expectedShowReverseChargeNote);
            expect(f.getVat()).toEqual(expectedVAT);
        }
    }));

});
