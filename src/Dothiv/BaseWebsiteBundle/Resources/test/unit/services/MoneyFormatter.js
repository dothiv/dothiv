'use strict';

function getDecimalTestData() {
    return [
        [500000.99, 'de', '€', '500.000 €'],
        ['500000.99', 'de', '€', '500.000 €'],
        [500000.99, 'en', '€', '€500,000'],
        ['500000.99', 'en', '€', '€500,000'],
        [500000.99, null, '€', '€500,000'],
        ['500000.99', null, '€', '€500,000'],
        [500000, 'de', '€', '500.000 €'],
        ['500000', 'de', '€', '500.000 €'],
        [500000, 'en', '€', '€500,000'],
        ['500000', 'en', '€', '€500,000'],
        [500000, null, '€', '€500,000'],
        ['500000', null, '€', '€500,000']
    ];
}

function getFloatTestData() {
    return [
        [500000.99, 'de', '€', '500.000,99 €'],
        ['500000.99', 'de', '€', '500.000,99 €'],
        [500000.99, 'en', '€', '€500,000.99'],
        ['500000.99', 'en', '€', '€500,000.99'],
        [500000.99, null, '€', '€500,000.99'],
        ['500000.99', null, '€', '€500,000.99'],
        [500000, 'de', '€', '500.000 €'],
        ['500000', 'de', '€', '500.000 €'],
        [500000, 'en', '€', '€500,000'],
        ['500000', 'en', '€', '€500,000'],
        [500000, null, '€', '€500,000'],
        ['500000', null, '€', '€500,000'],
        [0.1234, 'de', '€', '0,12 €'],
        ['0.1234', 'de', '€', '0,12 €'],
        [0.1234, 'en', '€', '€0.12'],
        ['0.1234', 'en', '€', '€0.12'],
        [0.1234, null, '€', '€0.12'],
        ['0.1234', null, '€', '€0.12'],
        [500000.999, 'en', '€', '€500,001']
    ];
}

describe('MoneyFormat service', function () {

    beforeEach(module('dotHIVApp.services'));

    it('it Should Format Decimal Money', inject(function (MoneyFormatter) {
        var d = getDecimalTestData();
        for (var k in d) {
            var input = d[k][0];
            var locale = d[k][1];
            var currency = d[k][2];
            var expected = d[k][3];
            var f = new MoneyFormatter(locale);
            expect(f.decimalFormat(input, currency)).toEqual(expected);
        }
    }));

    it('it Should Format Real Money', inject(function (MoneyFormatter) {
        var d = getFloatTestData();
        for (var k in d) {
            var input = d[k][0];
            var locale = d[k][1];
            var currency = d[k][2];
            var expected = d[k][3];
            var f = new MoneyFormatter(locale);
            expect(f.format(input, currency)).toEqual(expected);
        }
    }));

});
