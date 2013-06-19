'use strict';

/* Directives */


angular.module('myApp.directives', [])
    .directive("repeatPassword", function() {
        return {
            require: "ngModel",
            link: function(scope, elem, attrs, ctrl) {
                var otherInput = elem.inheritedData("$formController")[attrs.repeatPassword];

                ctrl.$parsers.push(function(value) {
                    if(value === otherInput.$viewValue) {
                        ctrl.$setValidity("repeat", true);
                        return value;
                    }
                    ctrl.$setValidity("repeat", false);
                });

                otherInput.$parsers.push(function(value) {
                    ctrl.$setValidity("repeat", value === ctrl.$viewValue);
                    return value;
                });
            }
        };
    })
    .directive("dhInput", function($compile) {
        return {
            restrict: 'E',
            scope: {
                    // core input attributes
                    name: '@dhName',
                    placeholder: '@dhPlaceholder',
                    ngModel: '=',
                    Required: '@dhRequired',
                    Autofocus: '@dhAutofocus',

                    // label
                    label: '@dhLabel',

                    // tooltip
                    tooltip: '@dhTooltip',
                    tooltipPlacement: '@dhTooltipPlacement',
                    tooltipTrigger: '@dhTooltipTrigger',
                    tooltipInvalid: '@dhTooltipInvalid',
                    tooltipShowInvalid: '=dhTooltipShowInvalid',
                },
            template: '<div>' +
                        '<label for="[[ name ]]" ng-transclude>[[ label ]]</label>' +
                        '<input ' +
                            // core input attributes
                            'name="[[ name ]]"' +
                            'id="[[ name ]]" ' +
                            'placeholder="[[ placeholder ]]"' +
                            'ng-model="$parent.ngModel"' +

                            // tooltip
                            'tooltip="[[ tooltiptext() ]]" ' +
                            'tooltip-placement="[[ tooltipPlacement ]]" ' +
                            'tooltip-trigger="[[ tooltipTrigger ]]" ' +
                            '/>' +
                      '</div>',
            replace: true,
            transclude: true,
            require: 'ngModel',
            priority: 10,
            controller: ['$scope', '$element', '$attrs', '$transclude', function($scope, $element, $attrs, $transclude) {
                $scope.tooltiptext = function() {
                    console.log($scope.tooltipShowInvalid ? 'valid' : 'invalid');
                    return $scope.tooltipShowInvalid ? $scope.tooltipInvalid : $scope.tooltip;
                }
            }],
            compile: function(tElement, tAttrs, transclude) {
                var input = tElement.find('input');

                // move css classes
                input.attr('class', tElement.attr('class'));
                tElement.removeAttr('class');

                // move type
                input.attr('type', tElement.attr('type'));
                tElement.removeAttr('type');

                // require attribute
                if (tElement.attr("dh-required") != undefined) {
                    input.prop("required", true);
                }

                // autofocus attribute
                if (tElement.attr("dh-autofocus") != undefined) {
                    input.prop("autofocus", true);
                }
            }
        };
    });
