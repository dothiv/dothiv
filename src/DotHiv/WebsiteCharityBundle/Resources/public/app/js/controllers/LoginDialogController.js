'use strict';

angular.module('dotHIVApp.controllers').controller('LoginDialogController', ['$scope', 'dialog', 'security', '$window', '$location', '$translate',
        function($scope, dialog, security, $window, $location, $translate) {
            $scope.loginclean = true;
            $scope.focusLoginUsername = 1;
            $scope.loginData = { 'username': '', 'password': '' };

            function resetLoginTooltip() {
                $scope.logintooltip = $translate('login.form.username.tooltip.default');
            }

            $scope.$watch('loginData.username', function() {
                resetLoginTooltip();
            })
            $scope.$watch('loginData.password', function() {
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
                            $scope.registrationerrormsg = error;
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
