'use strict';

angular.module('dotHIVApp.services').factory('dothivAccountResource', ['$resource', function ($resource) {
    return $resource('/api/account/:handle', {}, {
        'create': {method: 'POST'},
        'requestLoginLink': {method: 'POST', params: {handle: 'loginLink'}}
    });
}]);
