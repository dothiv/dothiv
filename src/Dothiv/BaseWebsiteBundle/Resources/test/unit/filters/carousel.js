'use strict';

describe('offset filter', function() {
    
    var carousel;
    
    beforeEach(module('dotHIVApp.filters'));
    beforeEach(inject(function($filter) {
        carousel = $filter('carousel');
    }));
    
    it('should cut place the first X elements at the end', function() {
        var a = ['1', '2', '3', '4'];
        var b = ['2', '3', '4', '1'];
        expect(carousel(a, 1)).toEqual(b);

        var a = ['1', '2', '3', '4'];
        var b = ['4', '1', '2', '3'];
        expect(carousel(a, 3)).toEqual(b);
    });
    
    it('should handle negative X-values as other-way-turns', function() {
        var a = ['1', '2', 'a', 'b'];
        var b = ['2', 'a', 'b', '1'];
        expect(carousel(a,-3)).toEqual(b);
    });
    
    it('should return the same array if nothing is shifted', function() {
        var a = ['1', '2'];
        expect(carousel(a,0)).toEqual(a);
    });
    
    it('the input array has not been modifiend', function() {
        var a = ['1', '2'];
        expect(carousel(a,0)).not.toBe(a);
    });
    
});