'use strict';

angular.module('dotHIVApp.services').factory('security', ['$http', 'dothivUserResource', 'User', '$state', function ($http, dothivUserResource, User, $state) {
    // variable to keep user information and login status
    var _state = {user: {}};
    _state.isAuthenticated = function () {
        return ('email' in _state.user);
    }

    // holds whether the _state is currently updated
    var _updating = false;

    function _storeCredentials(handle, authToken) {
        User.setHandle(handle);
        User.setAuthToken(authToken);
        $http.defaults.headers.common['Authorization'] = 'Bearer ' + User.getAuthToken();
    }

    function _clearCredentials() {
        User.setHandle(null);
        User.setAuthToken(null);
        delete $http.defaults.headers.common['Authorization'];
    }

    function _logout(callback) {
        dothivUserResource.clearToken(
            {
                handle: User.getHandle()
            },
            // on success
            function (value, headers) {
                _state = {user: {}};
                $state.transitionTo('login');
                _clearCredentials();
                (callback || angular.noop)(value, headers);
            },
            // on error
            function (data, status, headers, config) {
                _state = {user: {}};
                $state.transitionTo('login');
                _clearCredentials();
                (callback || angular.noop)(false, data);
            }
        );
    }

    function _updateUserInfo(callback) {
        _onUpdateStarting();
        _state.user = dothivUserResource.get(
            {
                handle: User.getHandle()
            },
            // on success
            function (value, headers) {
                _onUpdateFinished();
                (callback || angular.noop)(value, headers);
            },
            // on error
            function (data, status, headers, config) {
                _onUpdateFinished();
                _logout();
                (callback || angular.noop)(false, data);
            }
        );
    }

    function _onUpdateStarting() {
        _updating = true;
    }

    function _onUpdateFinished() {
        _updating = false;
    }

    return {
        logout: _logout,
        updateUserInfo: _updateUserInfo,
        state: _state,
        storeCredentials: _storeCredentials
    };
}]);
