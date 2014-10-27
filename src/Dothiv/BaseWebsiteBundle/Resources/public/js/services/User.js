'use strict';

angular.module('dotHIVApp.services').factory('User', [function () {
    var _auth_token;
    var _handle;

    function _getAuthToken() {
        return _auth_token;
    }

    function _setAuthToken(auth_token) {
        _auth_token = auth_token;
    }

    function _getHandle() {
        return _handle;
    }

    function _setHandle(handle) {
        _handle = handle;
    }

    return {
        getAuthToken: _getAuthToken,
        setAuthToken: _setAuthToken,
        getHandle: _getHandle,
        setHandle: _setHandle
    };
}]);
