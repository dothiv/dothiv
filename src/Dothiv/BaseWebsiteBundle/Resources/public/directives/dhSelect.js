'use strict';

/**
 * This directive provides a custom select option dropdown menu.
 */
angular.module('dotHIVApp.directives').directive("dhSelect", function($compile) {
    return {
        restrict: 'E',
        scope: {
                model: '=model',
                options: '=options',
                label: '@label',
                defaultlabel: '@defaultlabel',
                assign: '@assign'
               },
        template: '<div class="dh-select">' +
                    '<a class="dropdown-toggle">[[ getLabel(modelObject) ]]<i class="dropdown-icon pull-right"></i></a>' +
                    '<ul class="dropdown-menu">' +
                      '<li ng-repeat="option in options">' +
                        '<a ng-click="select(option)" class="[[ key == model ? \'selected\' : \'\' ]]">[[ getLabel(option) ]]</a>' +
                      '</li>' +
                    '</ul>' +
                  '</div>',
        replace: true,
        priority: 10,
        controller: function($scope) {

            // update modelObject when model was changed
            $scope.$watch('model', function() {
                if ($scope.assign) {

                    // check if we do not need to search the optionect
                    if ($scope.modelObject && $scope.modelObject[$scope.assign] === $scope.model)
                        return;

                    // find modelObject in options
                    angular.forEach($scope.options, function(option) {
                        if (angular.isDefined(option[$scope.assign]) && option[$scope.assign] === $scope.model) {
                            $scope.modelObject = option;
                        }
                    });

                } else {
                    // if assign is empty, model === modelObject
                    $scope.modelObject = $scope.model;
                }
            });

            // select an option
            $scope.select = function(option) {
                $scope.modelObject = option; // use shortcut in $watch above
                $scope.model = $scope.assign ? option[$scope.assign] : option;
            };

            // get the label for an option
            $scope.getLabel = function(option) {
                if (!angular.isDefined(option)) return $scope.defaultlabel;
                return $scope.label ? option[$scope.label] : option;
            };

        }
    };
});
