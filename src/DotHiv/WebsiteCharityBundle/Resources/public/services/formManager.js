'use strict';

angular.module('dotHIVApp.services').factory('formManager', function($translate) {
    return function(name, $scope) {
        // set up our environment
        $scope.$watch(name, function() {
            if (!angular.isDefined($scope[name].data))
                $scope[name].data = {};
            if (!angular.isDefined($scope[name].focus))
                $scope[name].focus = {};
            if (!angular.isDefined($scope[name].tooltip))
                $scope[name].tooltip = {};
            if (!angular.isDefined($scope[name].failed))
                $scope[name].failed = false;

            // update tooltips whenever the form data changes
            $scope.$watch(name + '.data', function() {
                updateTooltips();
            }, true);
            updateTooltips();
        });

        function updateTooltips() {
            forEachInput($scope[name], function(value, key) {
                var translateKey = name + '.form.' + key + '.tooltip';
                if (value.$invalid && $scope[name].failed) {
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
                $scope[name].tooltip[key] = $translate(translateKey);
            });
        }

        function forEachInput(array, fn) {
            angular.forEach(array, function(value, key) {
                if (key != "" && key[0] == '$')
                    return;
                if (!angular.isDefined(value.$invalid))
                    return;
                fn(value, key);
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
            angular.isDefined($scope[name].focus[key]) ? $scope[name].focus[key]++ : $scope[name].focus[key] = 1;
        }

        function setTooltipFor(key, tooltip) {
            $scope[name].tooltip[key] = tooltip;
        }

        var manager =
        {
            showServerError: function(error) {
                var key = firstInputName($scope[name]);
                $scope[name].tooltip[key] = error;
                setFocusOn(key);
            },
            showServerFormError: function(form) {
                if (!angular.isDefined(form.data.form.children)) {
                    return;
                }

                var errorFound = false;
                angular.forEach(form.data.form.children, function(value, key) {
                    if (errorFound) return;

                    if (angular.isDefined(value.errors)) {
                        setTooltipFor(key, value.errors[0]);
                        setFocusOn(key);
                        errorFound = true;
                    }
                });
            },
            fail: function() {
                $scope[name].failed = true;

                // set focus to the first invalid field
                var breakloop = false;
                angular.forEach($scope[name], function(value, key) {
                    if (breakloop)
                        return;
                    if (key[0] == '$')
                        return;
                    if (value.$invalid) {
                        setFocusOn(key);
                        breakloop = true;
                    }
                });

                updateTooltips();
            }
        };
        return manager;
    };
});
