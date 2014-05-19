'use strict';
/**
 * @name dotHIVApp.services.formManager
 * @requires $translate
 *
 * @description
 * Provides $tooltip info and error messages for a form instance.
 *
 * The formManager manages the following fields inside the $scope.formName object:
 * - `$scope.formName.$tooltip` {string}
 * 
 * The formManager provides the following method:
 *
 * - **`function(name,scope)`** Log the user in. Arguments are:
 *   - `name`  – {string} The name of the form to manage
 *   - `scope` – {string} The scope in which the form resides.
 *
 *   The manager then returns a management object for the form, which provides the following methods:
 *   - fail() Mark the form as failed. That is, set the failed variable on the form to true, update the
 *            $tooltip texts and set the $focus to the first $invalid field.
 *   - showServerError(text) Show an plain text error message on the first field.
 *     - `text`  – {string} The plain text error message to show.
 *   - showServerFormError(obj) Show the first error message from the given object on the respective
 *                              form input field.
 *     - `obj`  – {obj} The form error object. Please see unit tests for detailed object structure.
 */
angular.module('dotHIVApp.services').factory('formManager', function($translate) {
    return function(name, $scope, dataName) {
        if (!dataName) dataName = name + '.$data';

        // set up our environment
        $scope.$watch(name, function() {
            if (!angular.isDefined($scope[name].$focus))
                $scope[name].$focus = {};
            if (!angular.isDefined($scope[name].$tooltip))
                $scope[name].$tooltip = {};
            if (!angular.isDefined($scope[name].$failed))
                $scope[name].$failed = false;

            // update $tooltips whenever the form $data changes
            $scope.$watch(dataName, function() {
                updateTooltips();
            }, true);
            updateTooltips();
        });

        function updateTooltips() {
            forEachInput($scope[name], function(value, key, path) {
                var translateKey = name + '.form' + path + '.' + key + '.tooltip';
                if (value.$invalid && $scope[name].$failed) {
                    // find error
                    var error = '';
                    angular.forEach(value.$error, function(evalue, ekey) {
                        if (evalue)
                            error = ekey;
                    });
                    // compile translation key
                    translateKey += '.invalid';
                    var specificTranslateKey = translateKey + (error != '' ? '.' + error : '');
                    if ($translate(translateKey) == translateKey)
                        translateKey = specificTranslateKey;
                } else {
                    translateKey += '.default';
                }
                $scope[name].$tooltip[key] = $translate(translateKey);
            });
        }

        function forEachInput(array, fn, path) {
            if (!path) path = '';

            angular.forEach(array, function(value, key) {
                if (!angular.isObject(value) || (key != "" && key[0] == '$'))
                    return;
                if (!('$modelValue' in value)) {
                    // recursve for subform
                    forEachInput(value, fn, path + '.' + key);
                } else {
                    // found input, call handle
                    fn(value, key, path);
                }
            }); 
        }

        function firstInputName(array) {
            var first = null;
            var breakloop = false;
            angular.forEach(array, function(value, key) {
                if (breakloop) return;
                if (key != "" && key[0] == '$') return;
                breakloop = true;
                first = key;
            });
            return first;
        }

        function setFocusOn(key) {
            angular.isDefined($scope[name].$focus[key]) ? $scope[name].$focus[key]++ : $scope[name].$focus[key] = 1;
        }

        function set$tooltipFor(key, $tooltip) {
            $scope[name].$tooltip[key] = $tooltip;
        }

        var manager =
        {
            updateTooltips: updateTooltips,
            showServerError: function(error) {
                var key = firstInputName($scope[name]);
                $scope[name].$tooltip[key] = error;
                setFocusOn(key);
            },
            showServerFormError: function(form) {
                if (!angular.isDefined(form.data.errors.children)) {
                    return;
                }

                var errorFound = false;
                angular.forEach(form.data.errors.children, function(value, key) {
                    if (errorFound) return;

                    if (angular.isDefined(value.errors)) {
                        set$tooltipFor(key, value.errors[0]);
                        setFocusOn(key);
                        errorFound = true;
                    }
                });
            },
            fail: function() {
                $scope[name].$failed = true;

                // set $focus to the first invalid field
                var breakloop = false;
                // reverse the order of errors (though that's an arbitrary choice, it seems like being more reasonable for the user)
                // TODO assign a focus priority to the input elements
                var errors = Object.keys($scope[name].$error || {}).reverse();
                angular.forEach(errors, function(key) {
                    var value = $scope[name].$error[key];
                    if (breakloop) return;
                    if (angular.isArray(value)) {
                        angular.forEach(value, function(element) {
                            if (breakloop) return;
                            if (angular.isDefined(element) && '$modelValue' in element) {
                                // element is an input field
                                if (element.$invalid) {
                                    breakloop = true;
                                    setFocusOn(element.$name);
                                }
                            } else {
                                // element is a sub form
                                forEachInput(element, function(element, name) {
                                    if (breakloop) return;
                                    if (element.$invalid) {
                                        breakloop = true;
                                        setFocusOn(name);
                                    }
                                });
                            }
                        });
                    }
                });

                updateTooltips();
            }
        };
        return manager;
    };
});
