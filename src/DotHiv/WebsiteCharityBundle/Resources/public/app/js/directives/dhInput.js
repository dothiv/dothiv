'use strict';

angular.module('dotHIVApp.directives').directive("dhInput", function($compile) {
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
                    '<label ng-transclude>[[ label ]]</label>' +
                    '<input ' +
                        // core input attributes
                        'name="[[ name ]]"' +
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
                return $scope.tooltipShowInvalid ? $scope.tooltipInvalid : $scope.tooltip;
            };
        }],
        compile: function(tElement, tAttrs, transclude) {
            var input = tElement.find('input');
            var label = tElement.find('label');
            var id = Math.random().toString(36).substring(7);

            // set random id
            input.attr('id', id);
            label.attr('for', id);

            // move css classes
            input.attr('class', tElement.attr('class'));
            tElement.removeAttr('class');

            // move type
            input.attr('type', tElement.attr('type'));
            tElement.removeAttr('type');

            // require attribute
            if (tElement.attr("dh-required") != undefined) {
                input.attr("required", "required");
            }

            // autofocus attribute
            if (tElement.attr("dh-autofocus") != undefined) {
                input.prop("autofocus", true);
            }

            // switch label and input for radio buttons and check boxes
            if (input.attr('type') == 'radio' || input.attr('type') == 'checkbox') {
                label.replaceWith(input);
                input.parent().append(label);
            }
        }
    };
});
