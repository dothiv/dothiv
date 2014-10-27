'use strict';

describe('offset filter', function () {

    beforeEach(module('dotHIVApp.filters'));

    it('should cut off first X elements',
        inject(function (offsetFilter) {
            var a = ['1', '2', '3', '4'];
            var b = ['2', '3', '4'];
            expect(offsetFilter(a, 1)).toEqual(b);

            var a = ['1', '2', '3', '4'];
            var b = ['4'];
            expect(offsetFilter(a, 3)).toEqual(b);
        })
    );

    it('should return empty array if too much is cut off',
        inject(function (offsetFilter) {
            var a = ['1'];
            expect(offsetFilter(a, 2)).toEqual([]);
        })
    );
});
