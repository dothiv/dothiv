'use strict';

/**
 * This directive provides a custom select option dropdown menu.
 */
angular.module('dotHIVApp.directives').directive("dhSelect", function($compile) {
    return {
        restrict: 'E',
        scope: {
                // core attributes
                ngModel: '=',
                options: '=dhOptions',
               },
        template: '<div class="dh-dropdown">' +
                    '<a class="dropdown-toggle">[[ ngModel ]]<i class="dropdown-icon pull-right"></i></a>' +
                    '<ul class="dropdown-menu">' +
                      '<li ng-repeat="(key, value) in options">' +
                        '<a ng-click="select(this)" dh-value="[[ key ]]" class="[[ value == ngModel ? \'selected\' : \'\' ]]">[[ value ]]</a>' +
                      '</li>' +
                    '</ul>' +
                  '</div>',
        replace: true,
        priority: 10,
        controller: function($scope) {
            $scope.select = function(selectedElement) {
                $scope.ngModel = selectedElement.value;
            };
        }
    };
});
