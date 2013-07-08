'use strict';

angular.module('dotHIVApp.services').factory('dothivLocaleResource', function($resource) {
    return $resource('/app_dev.php/api/locale', {}, {
        'get':    {method:'GET'},
        'put':    {method:'PUT'},
    });
});
