'use strict';

/* Services */
var myModule = angular.module('myApp.services', ['ui.bootstrap', 'myApp.controllers']);

myModule.factory('security', function($http, $templateCache, authService) {
    // private variable keeping track of authentication status
    var isAuthenticated = false;

    function _login(username, password, callback) {
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
    }

    function _logout(callback) {
        $http.get('/app_dev.php/logout').success(function() {
            isAuthenticated = false;
            $templateCache.removeAll();
            (callback || angular.noop)();
        });
    }

    function _register(username, email, password, callback) {
        // TODO replace this by $resource API call
        $http.post('/app_dev.php/api/users', {
            username: username,
            email: email,
            plainPassword: password
        }).success(function() {
            // direct login
            _login(username, password);
            (callback || angular.noop)(true);
        }).error(function(data, status, headers, config) {
            (callback || angular.noop)(false, data);
        });
    }

    function _updateIsAuthenticated(callback) {
        $http.get('/app_dev.php/api/login_state').success(function() {
            isAuthenticated = true;
            (callback || angular.noop)();
        }).error(function(data, status, headers, config) {
            isAuthenticated = false;
            (callback || angular.noop)();
        });
    }

    function _isAuthenticated() {
        return isAuthenticated; 
    }

    return {
        login: _login,
        updateIsAuthenticated: _updateIsAuthenticated,
        isAuthenticated: _isAuthenticated,
        register: _register,
        logout: _logout
    };
});

myModule.factory('securityDialog', function($dialog) {
    function _showLogin() {
        $dialog.dialog({
            keyboard: true, // TODO make these values default
            backdropClick: true, // TODO make these values default
            dialogFade: true, // TODO make these values default
            backdropFade: true, // TODO make these values default
            templateUrl: '/app_dev.php/partial/login',
            controller: 'SecurityLoginDialogController'
        }).open();
    }

    function _showRegistration() {
        $dialog.dialog({
            keyboard: true, // TODO make these values default
            backdropClick: true, // TODO make these values default
            dialogFade: true, // TODO make these values default
            backdropFade: true, // TODO make these values default
            templateUrl: '/app_dev.php/partial/registration',
            controller: 'SecurityRegistrationDialogController'
        }).open();
    }

    return {
        showLogin: _showLogin,
        showRegistration: _showRegistration
    };
});
