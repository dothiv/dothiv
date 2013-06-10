'use strict';

/* Controllers */

angular.module('myApp.controllers', ['http-auth-interceptor', 'ui.bootstrap', 'myApp.services']).
    controller('HomeController', ['$scope', '$http', 'authService', '$dialog', 'securityDialog', 
        function($scope, $http, authService, $dialog, securityDialog) {

            // TODO move to a more general place
            $scope.$on('event:auth-loginRequired', function() {
                securityDialog.showLogin();
              });
            
            $scope.$on('event:auth-loginConfirmed', function() {
                console.log('login confirmed');
              });
        
        }
    ])
    .controller('HeaderController', ['$scope', 'security', 'securityDialog',
        function($scope, security, securityDialog) {
        
            $scope.isAuthenticated = function() {
                return security.isAuthenticated();
            };
            
            $scope.login = function() {
                securityDialog.showLogin();
            };
            
            $scope.logout = function() {
                security.logout();
            };
            
            $scope.register = function() {
                securityDialog.showRegistration();
            };
            
            $scope.security = security.state;
            
            // TODO move this to a more general place
            security.updateUserInfo();
        
        }
    ])
    .controller('SecurityLoginDialogController', ['$scope', 'dialog', 'security',
        function($scope, dialog, security) {
            $scope.loginclean = true;

            $scope.login = function(data) {
                if($scope.loginForm.$invalid) {
                    $scope.loginclean = false;
                    console.log("still invalid");
                } else {
                    console.log("form valid");
                    security.login(data.username, data.password, function(result, error) {
                        if (result) {
                            // login successful
                            console.log("login successful");
                            dialog.close();
                        } else {
                            // login failed
                            console.log("login failed");
                            $scope.loginerrormsg = error;
                        }
                    });
                }
            };

            $scope.registrationclean = true;

            $scope.register = function(data) {
                if ($scope.registrationForm.$invalid) {
                    $scope.registrationclean = false;
                    console.log("still invalid");
                } else {
                    console.log("form valid");
                    security.register(data.name, data.surname, data.email, data.password, function(result, error) {
                        if (result) {
                            // registration successful
                            console.log("registration successful");
                            dialog.close();
                        } else {
                            // registration failed
                            console.log("registration failed");
                            $scope.registrationerrormsg = error;
                        }
                    });
                }
            };

            $scope.abort = function() {
                dialog.close();
            };
        }
    ])
