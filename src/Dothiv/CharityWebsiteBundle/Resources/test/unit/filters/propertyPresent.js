'use strict';

describe('propertyPresent filter', function () {

    beforeEach(module('dotHIVApp.filters'));

    it('should return press quotes which should be shown and have a logo but no quote',
        inject(function (propertyPresentFilter) {
            var a = [
                {show: false},
                {show: true},
                {show: true, logo: {}},
                {show: true, logo: {}, quote: "some quote"}
            ];
            var b = [
                {show: true, logo: {}}
            ];
            expect(propertyPresentFilter(a, {show: true, logo: true, quote: false})).toEqual(b);
        })
    );

    it('should return press quotes which should be shown and have a quote and optionally a logo',
        inject(function (propertyPresentFilter) {
            var a = [
                {show: false},
                {show: true},
                {show: true, logo: {}},
                {show: true, logo: {}, quote: "some quote"},
                {show: true, quote: "some quote"}
            ];
            var b = [
                {show: true, logo: {}, quote: "some quote"},
                {show: true, quote: "some quote"}
            ];
            expect(propertyPresentFilter(a, {show: true, logo: null, quote: true})).toEqual(b);
        })
    );

});
