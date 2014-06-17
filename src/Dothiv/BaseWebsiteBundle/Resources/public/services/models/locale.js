'use strict';

angular.module('dotHIVApp.services').factory('dothivLocaleResource', function($resource) {
    return $resource('api/locale', {}, {
        'get':    {method:'GET'},
        'put':    {method:'PUT'},
    });
});
