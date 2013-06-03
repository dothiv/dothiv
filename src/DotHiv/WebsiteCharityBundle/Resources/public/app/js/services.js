'use strict';

/* Services */
var myModule = angular.module('myApp.services', ['ui.bootstrap', 'myApp.controllers']);

myModule.factory('security', function($http, authService) {
    var isAuthenticated = false;
    var security = {
            login: function(username, password, callback) {
                $http({
                    method: 'POST',
                    url: '/app_dev.php/login_check',
                    data: '_username=' + username + '&_password=' + password,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).success(function() {
                    authService.loginConfirmed();
                    isAuthenticated = true;
                    (callback || angular.noop)(isAuthenticated);
                }).error(function(data, status, headers, config) {
                    isAuthenticated = false;
                    (callback || angular.noop)(isAuthenticated, data);
                });
            },
            logout: function(callback) {
                $http.get('/app_dev.php/logout').success(function() {
                    isAuthenticated = false;
                    (callback || angular.noop)();
                });
            },
            register: function(username, email, password, callback) {
                // TODO replace this by $resource API call
                $http.post('/app_dev.php/api/users', {
                    username: username,
                    email: email,
                    plainPassword: password
                }).success(function() {
                    // TODO direct login
                    (callback || angular.noop)(true);
                }).error(function(data, status, headers, config) {
                    (callback || angular.noop)(false, data);
                });
            },
            updateIsAuthenticated: function(callback) {
                $http.get('/app_dev.php/api/login_state').success(function() {
                    isAuthenticated = true;
                    (callback || angular.noop)();
                }).error(function(data, status, headers, config) {
                    isAuthenticated = false;
                    (callback || angular.noop)();
                });
            },
            isAuthenticated: function() {
                return isAuthenticated;
            },

    };
    return security;
});

myModule.factory('securityDialog', function($dialog) {
    var securityDialog = {
        showLogin: function() {
            $dialog.dialog({
                keyboard: true, // TODO make these values default
                backdropClick: true, // TODO make these values default
                dialogFade: true, // TODO make these values default
                backdropFade: true, // TODO make these values default
                templateUrl: '/app_dev.php/partial/login',
                controller: 'SecurityLoginDialogController'
            }).open();
        },
        showRegistration: function() {
            $dialog.dialog({
                keyboard: true, // TODO make these values default
                backdropClick: true, // TODO make these values default
                dialogFade: true, // TODO make these values default
                backdropFade: true, // TODO make these values default
                templateUrl: '/app_dev.php/partial/registration',
                controller: 'SecurityRegistrationDialogController'
            }).open();
        },
    };
    return securityDialog;
});
