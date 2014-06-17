'use strict';

/**
 * @name dotHIVApp.filters.offset
 *
 * @description
 * Filter to be used in ng-repeat for scrolling purpose. 
 * Tells how many elements to be cut off at the beginning of the list. 
 */

angular.module('dotHIVApp.filters').filter('offset', function() {
        return function(input, start) {
            return input.slice(+start);
        }
});
