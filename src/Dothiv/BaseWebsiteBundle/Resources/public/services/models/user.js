'use strict';

angular.module('dotHIVApp.services').factory('dothivUserResource', function($resource, dothivResourceDefaultActions) {
    return $resource('api/users/:username/:sub', {}, {
        'get':        {method:'GET', params: {username: '@username'}},
        'getDomains': {method:'GET', isArray:true, params: {username: '@username', sub: 'domains'}},
        'update':     {method:'PUT', params: {username: '@username'}},
        'save':       {method:'POST', params: {username: ''}},
    });
});
