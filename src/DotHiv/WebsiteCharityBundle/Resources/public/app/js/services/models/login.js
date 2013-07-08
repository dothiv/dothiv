'use strict';

angular.module('dotHIVApp.services').factory('dothivLoginResource', function($resource) {
    return $resource('/app_dev.php/api/login', {}, {
        'get':    {method:'GET'},
        'login':  {method:'POST'},
        'logout': {method:'DELETE'}
    });
});
