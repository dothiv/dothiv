'use strict';

/**
 * @name dotHIVApp.services.security
 * @requires $http, authServer, dothivLoginResource, dothivUserResource, $state
 * 
 * @description
 * Manages the user login and registration within the dothiv web application.
 * 
 * The returned object provides the following methods and fields:
 * 
 * - **`login()`** Log the user in. Arguments are:
 *   - `username` – {string}
 *   - `password` – {string}
 *   - `callback` – {function(success, data)} Callback to call after the request returns from the server. `success`
 *                  is a boolean variable indicating the success of the login, data is only given on failure and contains
 *                  the data sent by the server.
 * - **`logout()`** Logs the user out. No error is thrown if nobody is logged in. If the application is currently in a state
 *                  that requires the user to be logged in (indicated by '='), the state is switched to 'home'. Argument is:
 *   - `callback` – {function(success)} Callback to call after the request returns from the server. `success` is a boolean
 *                  variable indicating the success of the logout.
 *
 *
 * - **`register()`** Register a new user. Arguments are:
 *   - `name` – {string}
 *   - `surname` – {string}
 *   - `email` – {string}
 *   - `password` – {string}
 *   - `callback` – {function(success, data)} Callback to call after the request returns from the server. `success`
 *                  is a boolean variable indicating the success of the registration, data is only given on failure and contains
 *                  the data sent by the server.
 *
 *
 * - **`isAuthenticated()`** Returns a boolean indicating whether the user is currently logged in.
 *
 *
 * - **`updateUserInfo()`** Forces the security service to contact the server and get the user object for the current session. Argument is:
 *   - `callback` – {function(success, data)} Callback to call after the request returns from the server. `success`
 *                  is a boolean variable indicating the success of the request, data is only given on failure and contains
 *                  the data sent by the server. Notice that an unsuccessful request **does not** indicate that the user is not logged in, nor
 *                  does a successful request mean the user is logged in.
 *
 *
 * - **`schedule()`** Schedules an operation (that is, a callback function) to be executed after the current security state update
 *                    finishes. A security state update is indicated by the private variable _updating and is triggered for example
 *                    by the login(), logout() and updateUserInfo() function.
 *                    If there is currently no update, the operation is executed immediately and synchronously.
 *                    All scheduled callbacks will be executed before the respective callback function of any update-function will
 *                    be called. That is, logout(function { console.log('foo'); }); schedule(function { console.log('bar'); }); will
 *                    first output 'bar', then 'foo', given that schedule is executed before the AJAX call returns from the server.
 *                    Argument is:
 *   - `callback` – {function()} The operation to be executed after the current update finishes (if any).
 *
 *
 * - **`state`** An object holding the current security state of the application. Field is:
 *   - `user` – the current user object as sent by the server.
 */
angular.module('dotHIVApp.services').factory('security', function($http, authService, dothivLoginResource, dothivUserResource, $state) {
    // variable to keep user information and login status
    var _state = {'user': {}};

    // holds whether the _state is currently updated
    var _updating = false;

    // holds operations to be executed after the current update has finished
    var _scheduled = [];

    function _login(username, password, callback) {
        _onUpdateStarting();
        _state.user = dothivLoginResource.login(
                // user credentials
                {'username': username, 'password': password},
                // on success
                function(){
                    _onUpdateFinished();
                    authService.loginConfirmed();
                    (callback || angular.noop)(true);
                }, 
                // on error
                function(data) {
                    _onUpdateFinished();
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
                    _state.user = _userCopy;
                    if ($state.includes('=')) {
                        $state.transitionTo('home');
                    }
                    (callback || angular.noop)(true);
                },
                function(data) {
                    (callback || angular.noop)(false);
                }
        );
    }

    function _register(firstname, surname, email, password, callback) {
        dothivUserResource.save(
                // user data
                {'email': email, 'plainPassword': password, 'firstname': firstname, 'surname': surname},
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
        _onUpdateStarting();
        _state.user = dothivLoginResource.get(
                // no data required
                {},
                // on success
                function(value, headers) {
                    _onUpdateFinished();
                    (callback || angular.noop)(value, headers);
                },
                // on error
                function(data, status, headers, config) {
                    _onUpdateFinished();
                    (callback || angular.noop)(false, data);
                }
        );
    }

    function _onUpdateStarting() {
        _updating = true;
    }

    function _onUpdateFinished() {
        angular.forEach(_scheduled, function(value) {
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

    function _isAuthenticated() {
        return ('username' in _state.user); 
    }

    return {
        login: _login,
        logout: _logout,

        updateUserInfo: _updateUserInfo,
        isAuthenticated: _isAuthenticated,
        schedule: _schedule,

        register: _register,

        state: _state,
    };
});
