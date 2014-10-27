'use strict';

describe('carousel filter', function () {

    beforeEach(module('dotHIVApp.filters'));

    it('should cut place the first X elements at the end', inject(function (carouselFilter) {
        var a = ['1', '2', '3', '4'];
        var b = ['2', '3', '4', '1'];
        expect(carouselFilter(a, 1)).toEqual(b);

        var a = ['1', '2', '3', '4'];
        var b = ['4', '1', '2', '3'];
        expect(carouselFilter(a, 3)).toEqual(b);
    }));

    it('should handle negative X-values as other-way-turns', inject(function (carouselFilter) {
        var a = ['1', '2', 'a', 'b'];
        var b = ['2', 'a', 'b', '1'];
        expect(carouselFilter(a, -3)).toEqual(b);
    }));

    it('should return the same array if nothing is shifted', inject(function (carouselFilter) {
        var a = ['1', '2'];
        expect(carouselFilter(a, 0)).toEqual(a);
    }));

    it('the input array has not been modifiend', inject(function (carouselFilter) {
        var a = ['1', '2'];
        expect(carouselFilter(a, 0)).not.toBe(a);
    }));

});
