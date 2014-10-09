'use strict';
/**
 * This directives creates a facebook share button
 *
 * @see http://stackoverflow.com/a/25006100
 *
 * Usage:
 *   <facebook url="http://payitforward.hiv/"></facebook>
 */
angular.module('dotHIVApp.directives').directive('facebook', ['$window', '$timeout', function ($window, $timeout) {
    var directive = {};
    directive.restrict = 'E';
    directive.compile = function (element, attributes) {
        return function ($scope, element, attributes) { // link function
            element.html('<div class="fb-like" data-href="' + attributes.url + '" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false" data-font="tahoma" data-action="recommend"></div>');
            $scope.$watch(
                function () {
                    return !!$window.FB;
                },
                function (fbIsReady) {
                    if (fbIsReady) {
                        $timeout(function () {
                            $window.FB.XFBML.parse(element.parent()[0]);
                        });
                    }
                }
            );
        };
    };
    return directive;
}]);
