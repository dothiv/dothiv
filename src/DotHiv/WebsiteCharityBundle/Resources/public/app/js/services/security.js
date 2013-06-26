'use strict';

angular.module('dotHIVApp.services').factory('security', function($http, $templateCache, authService, dothivLoginResource, dothivUserResource) {
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
                {'email': email, 'plainPassword': password, 'name': name, 'surname': surname},
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
        state: _state
    };
});
