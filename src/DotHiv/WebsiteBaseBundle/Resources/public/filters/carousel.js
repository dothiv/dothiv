'use strict';

/**
 * @name dotHIVApp.filters.offset
 *
 * @description
 * Filter to be used in ng-repeat for carousel-like scrolling purpose. 
 * Tells how many elements to be moved from the beginning of the list to its end. 
 */

angular.module('dotHIVApp.filters').filter('carousel', function() {
        return function(input, shift) {
            var retr = new Array().concat(input);
            
            if (+shift < 0) {
                shift = (shift % retr.length) + retr.length;
            }
            
            for (var i = 0 ; i < +shift ; i++) {
                var tmp = retr[0];
                retr.shift();
                retr.push(tmp);
            }
            return retr;
        }
});
