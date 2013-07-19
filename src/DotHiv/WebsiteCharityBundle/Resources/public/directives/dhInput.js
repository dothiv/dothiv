'use strict';

angular.module('dotHIVApp.directives').directive("dhInput", function($compile, $timeout) {
    return {
        restrict: 'E',
        scope: {
                // core input attributes
                name: '@dhName',
                placeholder: '@dhPlaceholder',
                ngModel: '=',
                NgRequired: '=dhNgRequired',
                Autofocus: '@dhAutofocus',
                value: '@dhValue',

                // focus
                focus: '=dhFocus',

                // label
                label: '@dhLabel',

                // tooltip
                tooltip: '=dhTooltip',
                tooltipPlacement: '@dhTooltipPlacement',
                tooltipTrigger: '@dhTooltipTrigger',
            },
        template: '<div>' +
                    '<label ng-transclude>[[ label ]]</label>' +
                    '<input ' +
                        // core input attributes
                        'placeholder="[[ placeholder ]]"' +
                        'value="[[ value ]]"' +
                        'ng-model="$parent.ngModel"' +
                        'ng-required="$parent.NgRequired"' +

                        // focus
                        'dh-focus="focus"' +

                        // tooltip
                        'tooltip="[[ tooltip ]]" ' +
                        'tooltip-placement="[[ tooltipPlacement ]]" ' +
                        'tooltip-trigger="[[ tooltipTrigger ]]" ' +
                        '/>' +
                  '</div>',
        replace: true,
        transclude: true,
        priority: 10,
        controller: ['$scope', '$element', '$attrs', '$transclude', function($scope, $element, $attrs, $transclude) {
        }],
        compile: function(tElement, tAttrs, transclude) {
            var input = tElement.find('input');
            var label = tElement.find('label');
            var id;

            // check if ID is given, otherwise generate random id
            if (tElement.attr("dh-id") != undefined)
                id = tElement.attr("dh-id");
            else
                id = Math.random().toString(36).substring(7);

            // set id
            input.attr('id', id);
            label.attr('for', id);

            // set name
            input.attr('name', tElement.attr('dh-name'));

            // move css classes
            input.attr('class', tElement.attr('class'));
            tElement.removeAttr('class');

            // move type
            input.attr('type', tElement.attr('type'));
            tElement.removeAttr('type');

            // autofocus attribute
            if (tElement.attr("dh-autofocus") != undefined) {
                input.prop("autofocus", true);
            }

            if (input.attr('type') == 'radio' || input.attr('type') == 'checkbox') {
                // switch label and input for radio buttons and check boxes
                label.replaceWith(input);
                input.parent().append(label);
            } else {
                // for other types of input, append '<span>' for ':after' handling
                input.parent().append('<span></span>');
            }

            // add css class to div
            tElement.addClass('dhInput');

            return;
        }
    };
});
