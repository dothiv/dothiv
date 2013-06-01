'use strict';

/* Services */


// Demonstrate how to register services
// In this case it is a simple value service.
var myModule = angular.module('myApp.services', ['ui.bootstrap']);
myModule.factory('security', function($dialog, $http, $state) {
    var isAuthenticated = false;
    var security = {
            login: function() {
                console.log("logging in ...");
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
                                });
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
            isAuthenticated: function() {
                return isAuthenticated;
            }
    };
    return security;
});
