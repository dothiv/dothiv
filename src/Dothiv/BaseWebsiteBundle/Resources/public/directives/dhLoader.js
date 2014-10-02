'use strict';

/**
 * @name dotHIVApp.directives.dhLoader
 * @requires $interpolate
 *
 * @description
 * Shows the default dothiv loader animation.
 */
angular.module('dotHIVApp.directives').directive('dhLoader', function($interpolate) {
    var startSym = $interpolate.startSymbol();
    var endSym = $interpolate.endSymbol();
    return {
        restrict: 'E',
        replace: 'true',
        template: '<img src="/bundles/dothivbasewebsite/images/loader.gif" alt="' + startSym + ' \'loading.imgalt\' | translate ' + endSym + '"/>'
    };
});
