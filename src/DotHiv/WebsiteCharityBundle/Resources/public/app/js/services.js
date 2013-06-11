'use strict';

/* Services */
var myModule = angular.module('myApp.services', ['ui.bootstrap', 'myApp.controllers', 'ngResource']);

myModule.factory('dothivResourceDefaultActions', function() {
    return {
            'get':    {method:'GET'},
            'save':   {method:'POST'},
            'query':  {method:'GET', isArray:true},
            'remove': {method:'DELETE'},
            'delete': {method:'DELETE'}
    };
});

myModule.factory('dothivUserResource', function($resource, dothivResourceDefaultActions) {
    return $resource('http://dothiv.bp/app_dev.php/api/users/:username', {}, {
        'get':    {method:'GET', params:{username: '@username'}},
        'save':   {method:'POST', params:{username: ''}},
        'update': {method:'PUT', params:{username: '@username'}},
    });
});

myModule.factory('dothivLoginResource', function($resource, dothivResourceDefaultActions) {
    return $resource('http://dothiv.bp/app_dev.php/api/login', {}, {
        'get':    {method:'GET'},
        'login':  {method:'POST'},
        'logout': {method:'DELETE'}
    });
});

myModule.factory('security', function($http, $templateCache, authService, dothivLoginResource, dothivUserResource) {
    // variable to keep user information and login status
    var _state = {'user': {}};

    function _login(username, password, callback) {
        _state.user = dothivLoginResource.login(
                // user credentials
                {'username': username, 'password': password},
                // on success
                function(){
                    authService.loginConfirmed();
                    (callback || angular.noop)(true);
                }, 
                // on error
                function(data) {
                    _state.user = {};
                    (callback || angular.noop)(false, data);
                }
        );
    }

    function _logout(callback) {
        // preserve user object in case logout fails
        var _userCopy = dothivLoginResource.logout(
                // no data required for logout
                {},
                // on success
                function() {
                    $templateCache.removeAll();
                    _state.user = _userCopy;
                    (callback || angular.noop)(true);
                },
                function(data) {
                    (callback || angular.noop)(false);
                }
        );
    }

    function _register(name, surname, email, password, callback) {
        dothivUserResource.save(
                // user data
                {'username': email, 'email': email, 'plainPassword': password, 'name': name, 'surname': surname},
                // on success
                function() {
                    // direct login
                    _login(email, password);
                    (callback || angular.noop)(true);
                },
                // on error
                function(data, status, headers, config) {
                    (callback || angular.noop)(false, data);
                }
        );
    }

    function _edit(name, surname, email, callback) {
        var _userCopy = dothivUserResource.update(
                // user data
                {'username': _state.user.username, 'name': name, 'surname': surname, 'email': email},
                // on success
                function() {
                    _state.user = _userCopy;
                    (callback || angular.noop)(true);
                },
                // on error
                function(data, status, headers, config) {
                    (callback || angular.noop)(false, data);
                }
        );
    }

    function _updateUserInfo(callback) {
        _state.user = dothivLoginResource.get(
                // no data required
                {},
                // on success
                callback,
                // on error
                function(data, status, headers, config) {
                    (callback || angular.noop)(false, data);
                }
        );
    }

    function _isAuthenticated() {
        return ('username' in _state.user); 
    }

    return {
        login: _login,
        updateUserInfo: _updateUserInfo,
        isAuthenticated: _isAuthenticated,
        register: _register,
        logout: _logout,
        edit: _edit,
        state: _state
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
