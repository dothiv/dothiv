'use strict';

/**
 * @name dotHIVApp.directives.dhFocus
 * @requires $timeout
 *
 * @description
 * Sets the focus to the given element when the given value is changed.
 * Can be used on any element that supports the DOM method focus().
 */
angular.module('dotHIVApp.directives').directive("dhFocus", function($timeout) {
    return function(scope, element, attrs) {
        scope.$watch(attrs.dhFocus, function(val) {
            if (angular.isDefined(val)) {
                $timeout( function () { 
                    element[0].focus(); 
                });
            }
        });
    };
});
