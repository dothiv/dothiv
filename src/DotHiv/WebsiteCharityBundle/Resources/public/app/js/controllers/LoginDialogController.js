'use strict';

angular.module('dotHIVApp.controllers').controller('LoginDialogController', ['$scope', 'dialog', 'security', '$window', '$location', '$translate',
        function($scope, dialog, security, $window, $location, $translate) {
            $scope.loginclean = true;
            $scope.focusLoginUsername = 1;
            $scope.loginData = { 'username': '', 'password': '' };

            function resetLoginTooltip() {
                $scope.logintooltip = $translate('login.form.username.tooltip.default');
            }

            $scope.$watch('loginData', function() {
                resetLoginTooltip();
            })
            resetLoginTooltip();

            $scope.login = function(data) {
                if($scope.loginForm.$invalid) {
                    $scope.loginclean = false;
                } else {
                    security.login(data.username, data.password, function(result, error) {
                        if (result) {
                            // login successful
                            dialog.close();
                        } else {
                            // login failed
                            $scope.logintooltip = error.data;
                            $scope.focusLoginUsername++;
                        }
                    });
                }
            };

            $scope.registrationclean = true;
            $scope.registrationfocus = {};

            function resetRegistrationTooltips() {
                $scope.registrationtooltip = 
                    {
                        "name":           { "default": $translate("registration.form.name.tooltip.default"), "invalid": $translate("registration.form.name.tooltip.invalid") },
                        "surname":        { "default": $translate("registration.form.surname.tooltip.default"), "invalid": $translate("registration.form.surname.tooltip.invalid") },
                        "email":          { "default": $translate("registration.form.email.tooltip.default"), "invalid": $translate("registration.form.email.tooltip.invalid") },
                        "password":       { "default": $translate("registration.form.password.tooltip.default"), "invalid": $translate("registration.form.password.tooltip.invalid") },
                        "passwordrepeat": { "default": $translate("registration.form.passwordrepeat.tooltip.default"), "invalid": $translate("registration.form.passwordrepeat.tooltip.invalid") },
                    };
            }
            $scope.$watch('registrationData', function() {
                resetRegistrationTooltips();
            });
            resetRegistrationTooltips();

            $scope.register = function(data) {
                if ($scope.registrationForm.$invalid) {
                    $scope.registrationclean = false;
                } else {
                    security.register(data.name, data.surname, data.email, data.password, function(result, error) {
                        if (result) {
                            // registration successful
                            dialog.close();
                        } else {
                            // registration failed
                            if (!angular.isDefined(error.data.form.children)) {
                                // TODO what shall we do?
                                console.log("something is seriously wrong.");
                                return;
                            }

                            var errorFound = false;
                            angular.forEach(error.data.form.children, function(value, key) {
                                if (errorFound) return;

                                if (angular.isDefined(value.errors)) {
                                    // set the tooltip accordingly
                                    $scope.registrationtooltip[key]['default'] = $scope.registrationtooltip[key]['invalid'] = value.errors[0];

                                    // set the focus to the errored field
                                    !$scope.registrationfocus[key] ? $scope.registrationfocus[key] = 1 : $scope.registrationfocus[key]++;

                                    // break for each
                                    errorFound = true;
                                }
                            });
                        }
                    });
                }
            };

            $scope.thirdparty = function(url) {
                var popup = $window.open(url, 'thirdpartyLogin', 'width=580,height=200,location=no,menubar=no', false);
                $window.addEventListener('message', receiveMessage, false);
            }

            function receiveMessage(event) {
                if (event.origin !== $location.protocol() + "://" + $location.host()) {
                    return;
                }

                if (event.data) {
                    security.updateUserInfo();
                    dialog.close();
                }
            };

            $scope.abort = function() {
                dialog.close();
            };
        }
    ]);
