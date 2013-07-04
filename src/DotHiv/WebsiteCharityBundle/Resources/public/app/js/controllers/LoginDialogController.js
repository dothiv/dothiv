'use strict';

angular.module('dotHIVApp.controllers').controller('LoginDialogController', ['$scope', 'dialog', 'security', '$window', '$location',
        function($scope, dialog, security, $window, $location) {
            $scope.loginclean = true;

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
                            $scope.loginerrormsg = error;
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
