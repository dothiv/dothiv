'use strict';

/**
 * @name dotHIVApp.directives.dhTeaser
 * @requires $interpolate, $translate
 *
 * @description
 * Takes a translation key prefix, say my.prefix, and displayes
 * my.prefix.text and a link for "further reading", using
 * my.prefix.dropdown as a label. On click, the text is expanded
 * and my.prefix.expand is also shown.
 */
angular.module('dotHIVApp.directives').directive('dhTeaser', function($interpolate) {
    var startSym = $interpolate.startSymbol();
    var endSym = $interpolate.endSymbol();
    return {
        restrict: 'E',
        replace: 'true',
        scope: {
            prefix: '@prefix',
        },
        template:
        '<span>' +
            '<p>' +
                '<span translate="' + startSym + 'prefix + \'.text\'' + endSym + '"></span> ' +
                '<a href="" ng-click="show=true" ng-hide="show"><span class="heading-arrow" ng-hide="show"></span><span translate="' + startSym + 'prefix + \'.dropdown\'' + endSym + '"></span></a>' + 
            '</p>' +
            '<p ng-show="show">' +
                '<span translate="' + startSym + 'prefix + \'.expand\'' + endSym + '"></span>' +
                '<a href="" ng-click="show=undefined" class="heading-arrow-rev" ng-show="show"></a>' +
            '</p>' +
        '</span>',
    };
});
