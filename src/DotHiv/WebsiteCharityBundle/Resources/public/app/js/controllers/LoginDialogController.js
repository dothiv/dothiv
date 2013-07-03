'use strict';

angular.module('dotHIVApp.controllers').controller('LoginDialogController', ['$scope', 'dialog', 'security', '$window',
        function($scope, dialog, security, $window) {
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
                $window.open(url, 'thirdpartyLogin', 'width=580,height=200,location=no,menubar=no', false);
            }

            $scope.abort = function() {
                dialog.close();
            };
        }
    ]);
