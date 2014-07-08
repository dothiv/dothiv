'use strict';

angular.module('dotHIVApp.services').factory('dothivAccountResource', ['$resource', function ($resource) {
    return $resource('/api/account/:handle', {}, {
        'requestLoginLink': {method: 'POST', params: {handle: 'loginLink'}}
    });
}]);
