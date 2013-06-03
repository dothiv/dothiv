'use strict';

/* Services */


// Demonstrate how to register services
// In this case it is a simple value service.
var myModule = angular.module('myApp.services', ['ui.bootstrap']);
myModule.factory('security', function($dialog, $http, $state) {
    var isAuthenticated = false;
    var loginFailed = false;
    var security = {
            login: function() {
                loginFailed = false;
                $dialog.dialog({
                    keyboard: true,
                    templateUrl: '/app_dev.php/partial/login',
                    backdropClick: true,
                    dialogFade: true,
                    backdropFade: true,
                    controller: function($scope, dialog, $http, authService) {
                            $scope.login = function(username, password) {
                                console.log("sending login data ...");
                                $http({
                                    method: 'POST',
                                    url: '/app_dev.php/login_check',
                                    data: '_username=' + username + '&_password=' + password,
                                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                                }).success(function() {
                                    console.log("login complete.");
                                    authService.loginConfirmed();
                                    isAuthenticated = true;
                                    dialog.close();
                                }).error(function(data, status, headers, config) {
                                    $scope.errormsg = data;
                                    loginFailed = true;
                                });
                            };
                            $scope.abort = function() {
                                dialog.close();
                            };
                            $scope.loginFailed = function() {
                                return loginFailed;
                            };
                        }
                }).open().then(function($state) {
                    // do something after login
                });
            },
            logout: function() {
                console.log("logging out ...");
                $http.get('/app_dev.php/logout').success(function() {
                    console.log("logout complete.");
                    isAuthenticated = false;
                    $state.transitionTo('home');
                });
            },
            updateIsAuthenticated: function() {
                $http.get('/app_dev.php/api/login_state').success(function() {
                    isAuthenticated = true;
                }).error(function(data, status, headers, config) {
                    isAuthenticated = false;
                });
            },
            isAuthenticated: function() {
                return isAuthenticated;
            },
            loginFailed: function() {
                return loginFailed;
            },
            register: function() {
                $dialog.dialog({
                    keyboard: true,
                    templateUrl: '/app_dev.php/partial/register',
                    backdropClick: true,
                    dialogFade: true,
                    backdropFade: true,
                    controller: function($scope, dialog) {
                        $scope.register = function(username, email, password) {
                            // TODO replace this by $resource API call
                            console.log("register " + username + "/" + email + "/" + password);
                            $http.post('/app_dev.php/api/users', {
                                username: username,
                                email: email,
                                plainPassword: password
                            }).success(function() {
                                // TODO login
                                dialog.close();
                            }).error(function(data, status, headers, config) {
                                $scope.errormsg = data;
                                $scope.registerFailed = true;
                            });
                        };
                        $scope.abort = function() {
                            dialog.close();
                        };
                    }
                }).open();
            }
    };
    return security;
});
