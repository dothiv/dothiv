'use strict';

describe('pressquote filter', function () {

    beforeEach(module('dotHIVApp.filters'));

    it('should return press quotes which should be shown',
        inject(function (pressquoteFilter) {
            var a = [
                {show: false},
                {show: true},
                {show: true, logo: {}}
            ];
            var b = [
                {show: true},
                {show: true, logo: {}}
            ];
            expect(pressquoteFilter(a)).toEqual(b);
        })
    );

    it('should return press quotes which should be shown and have a logo but no quote',
        inject(function (pressquoteFilter) {
            var a = [
                {show: false},
                {show: true},
                {show: true, logo: {}},
                {show: true, logo: {}, quote: "some quote"}
            ];
            var b = [
                {show: true, logo: {}}
            ];
            expect(pressquoteFilter(a, {logo: true, quote: false})).toEqual(b);
        })
    );

    it('should return press quotes which should be shown and have a quote and optionally a logo',
        inject(function (pressquoteFilter) {
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
            expect(pressquoteFilter(a, {logo: null, quote: true})).toEqual(b);
        })
    );

});
