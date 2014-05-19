'use strict';

angular.module('dotHIVApp.services').factory('dothivResourceDefaultActions', function() {
    return {
            'get':    {method:'GET'},
            'save':   {method:'POST'},
            'query':  {method:'GET', isArray:true},
            'remove': {method:'DELETE'},
            'delete': {method:'DELETE'}
    };
});
