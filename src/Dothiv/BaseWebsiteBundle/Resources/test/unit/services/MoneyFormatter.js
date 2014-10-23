'use strict';

function getDecimalTestData() {
    return [
        [500000, 'de', '€', '500.000 €'],
        ['500000', 'de', '€', '500.000 €'],
        [500000, 'en', '€', '€500,000'],
        ['500000', 'en', '€', '€500,000'],
        [500000, null, '€', '€500,000'],
        ['500000', null, '€', '€500,000']
    ];
}

describe('MoneyFormat service', function () {

    beforeEach(module('dotHIVApp.services'));

    it('it Should Format Decimal Money', inject(function (MoneyFormatter) {
        var d = getDecimalTestData();
        for(var k in d) {
            var input = d[k][0];
            var locale = d[k][1];
            var currency = d[k][2];
            var expected = d[k][3];
            var f = new MoneyFormatter(locale);
            expect(f.decimalFormat(input, currency)).toEqual(expected);
        }
    }));

});
