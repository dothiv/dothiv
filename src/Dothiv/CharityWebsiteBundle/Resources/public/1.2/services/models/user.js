'use strict';

angular.module('dotHIVApp.services').factory('dothivUserResource', ['$resource', function ($resource) {
    return $resource('/api/user/:handle/:sub', {}, {
        'get': {method: 'GET', params: {handle: '@handle'}},
        'getDomains': {method: 'GET', isArray: true, params: {handle: '@handle', sub: 'domains'}},
        'clearToken': {method: 'DELETE', params: {handle: '@handle', sub: 'token'}}
    });
}]);
