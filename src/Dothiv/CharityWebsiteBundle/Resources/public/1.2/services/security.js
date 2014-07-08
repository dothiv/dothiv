'use strict';

angular.module('dotHIVApp.services').factory('security', ['$http', 'dothivUserResource', 'User', '$state', function ($http, dothivUserResource, User, $state) {
    // variable to keep user information and login status
    var _state = {'user': {}};

    // holds whether the _state is currently updated
    var _updating = false;

    function _logout(callback) {
        // preserve user object in case logout fails
        var _userCopy = dothivUserResource.logout(
            // no data required for logout
            {
                handle: User.getHandle()
            },
            // on success
            function () {
                _state.user = _userCopy;
                if ($state.includes('=')) {
                    $state.transitionTo('home');
                }
                (callback || angular.noop)(true);
            },
            function (data) {
                (callback || angular.noop)(false);
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
                $state.transitionTo('login');
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

    function _isAuthenticated() {
        return ('email' in _state.user);
    }

    return {
        logout: _logout,
        updateUserInfo: _updateUserInfo,
        isAuthenticated: _isAuthenticated,
        state: _state
    };
}]);
