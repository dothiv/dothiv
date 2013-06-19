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
    .controller('HeaderController', ['$scope', '$state', 'security', 'securityDialog',
        function($scope, $state, security, securityDialog) {
            // make state information available
            $scope.state = $state;
            
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
            
            $scope.bar = {
                'total': 10,
                'current': 1.43,
            }; 
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
    .controller('ProfileController', ['$scope', '$location', '$state', 'security',
        function($scope, $location, $state, security) {
            // make user information available
            $scope.security = security.state;

            // make current state information available
            $scope.state = $state;

            // make logout available and redirect to home page
            $scope.logout = function() {
                security.logout(function(success){
                    if (success)
                        $location.path( "/" );
                });
            };
        }
    ])
    .controller('ProfileEditController', ['$scope', '$location', 'security', 'dothivUserResource',
        function($scope, $location, security, dothivUserResource) {
        // get fresh user object
        $scope.user = dothivUserResource.get({"username": security.state.user.username});

        // send user object back to server
        $scope.submit = function() {
            $scope.user.$update(
                {"username": security.state.user.username},
                function() { // success
                    security.updateUserInfo();
                    $location.path( "/profile" );
                }, 
                function() { // error
                }
            );
        };
        }
    ]);
