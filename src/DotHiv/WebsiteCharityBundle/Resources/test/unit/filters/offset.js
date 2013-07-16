'use strict';

describe('offset filter', function() {
    
    var offset;
    
    beforeEach(module('dotHIVApp.filters'));
    beforeEach(inject(function($filter) {
        offset = $filter('offset');
    }));
    
    it('should cut off first X elements', function() {
        var a = ['1', '2', '3', '4'];
        var b = ['2', '3', '4'];
        expect(offset(a, 1)).toEqual(b);

        var a = ['1', '2', '3', '4'];
        var b = ['4'];
        expect(offset(a, 3)).toEqual(b);
    });
    
    it('should return empty array if too much is cut off', function() {
        var a = ['1'];
        expect(offset(a,2)).toEqual([]);
    });
    
});
