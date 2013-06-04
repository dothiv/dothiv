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
            
            // TODO move this to a more general place
            security.updateIsAuthenticated();
        
        }
    ])
    .controller('SecurityLoginDialogController', ['$scope', 'dialog', 'security',
        function($scope, dialog, security) {

            $scope.submit = function(username, password) {
                security.login(username, password, function(result, error) {
                    if (result) {
                        // login successful
                        console.log("login successful");
                        dialog.close();
                    } else {
                        // login failed
                        console.log("login failed");
                        $scope.errormsg = error;
                    }
                })
            };

            $scope.abort = function() {
                dialog.close(false);
            };

            $scope.loginFailed = false;

        }
    ])
    .controller('SecurityRegistrationDialogController', ['$scope', 'dialog', 'security',
        function($scope, dialog, security) {
            $scope.clean = true;
            $scope.submit = function(data) {
                if ($scope.registration.$invalid) {
                    $scope.clean = false;
                    console.log("still invalid");
                } else {
                    console.log("form valid");
                    security.register(data.username, data.email, data.password, function(result, error) {
                        if (result) {
                            // registration successful
                            console.log("registration successful");
                            dialog.close();
                        } else {
                            // registration failed
                            console.log("registration failed");
                            $scope.errormsg = error;
                        }
                    });
                }
            };
            $scope.abort = function() {
                dialog.close();
            };
        }
    ])
