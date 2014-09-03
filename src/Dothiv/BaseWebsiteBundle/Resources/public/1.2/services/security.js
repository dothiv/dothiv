'use strict';

angular.module('dotHIVApp.services').factory('security', ['$http', 'dothivUserResource', 'User', '$cookies', '$q', function ($http, dothivUserResource, User, $cookies, $q) {
    // variable to keep user information and login status
    var _user = $q.defer();

    function _isAuthenticated() {
        return ('email' in _user);
    }

    // holds whether the state is currently updated
    var _updating = false;

    // holds operations to be executed after the current update has finished
    var _scheduled = [];

    function _storeCredentials(handle, authToken) {
        User.setHandle(handle);
        User.setAuthToken(authToken);
        $cookies.securityUserHandle = handle;
        $cookies.securityAuthToken = authToken;
        $http.defaults.headers.common['Authorization'] = 'Bearer ' + User.getAuthToken();
    }

    function _clearCredentials() {
        User.setHandle(null);
        User.setAuthToken(null);
        delete $cookies.securityUserHandle;
        delete $cookies.securityAuthToken;
        delete $http.defaults.headers.common['Authorization'];
    }

    function _logout(callback) {
        dothivUserResource.clearToken(
            {
                handle: User.getHandle()
            },
            // on success
            function (value, headers) {
                (callback || angular.noop)(value, headers);
            },
            // on error
            function (data, status, headers, config) {
                (callback || angular.noop)(false, data);
            }
        );
        _user = $q.defer();
        _user.resolve({});
        _clearCredentials();
    }

    function _loadCookieCredentials() {
        if (typeof $cookies.securityUserHandle == "undefined") {
            return;
        }
        if (typeof $cookies.securityAuthToken == "undefined") {
            return;
        }
        if ($cookies.securityUserHandle.length <= 0) {
            return;
        }
        if ($cookies.securityAuthToken.length <= 0) {
            return;
        }
        _storeCredentials($cookies.securityUserHandle, $cookies.securityAuthToken);
    }

    function _updateUserInfo(callback) {
        _onUpdateStarting();
        _loadCookieCredentials();
        dothivUserResource.get(
            {
                handle: User.getHandle()
            },
            // on success
            function (value, headers) {
                _user.email = value.email;
                _user.firstname = value.firstname;
                _user.surname = value.surname;
                _user.$resolved = true;
                _onUpdateFinished();
                (callback || angular.noop)(value, headers);
            },
            // on error
            function (data, status, headers, config) {
                _logout();
                _onUpdateFinished();
                (callback || angular.noop)(false, data);
            }
        );
    }

    function _onUpdateStarting() {
        _updating = true;
    }

    function _onUpdateFinished() {
        angular.forEach(_scheduled, function (value) {
            (value || angular.noop)();
        });
        _scheduled = [];
        _updating = false;
    }

    function _schedule(callback) {
        if (_updating) {
            _scheduled.push(callback);
        } else {
            (callback || angular.noop)();
        }
    }

    return {
        schedule: _schedule,
        logout: _logout,
        updateUserInfo: _updateUserInfo,
        storeCredentials: _storeCredentials,
        isAuthenticated: _isAuthenticated,
        user: _user
    };
}]);
