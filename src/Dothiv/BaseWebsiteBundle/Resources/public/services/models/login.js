'use strict';

angular.module('dotHIVApp.services').factory('dothivLoginResource', function($resource) {
    return $resource('api/login', {}, {
        'get':    {method:'GET'},
        'login':  {method:'POST'},
        'logout': {method:'DELETE'}
    });
});
