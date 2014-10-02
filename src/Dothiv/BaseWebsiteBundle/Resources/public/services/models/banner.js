'use strict';

angular.module('dotHIVApp.services').factory('Banner', function($resource, dothivResourceDefaultActions) {
    return $resource('api/banners/:id', {id:'@id'}, {
        'get':        {method:'GET'},
        'query':      {method:'GET', isArray:true},
        'search':     {method:'GET', params: {token:'@token'}},
        'save':       {method:'POST', params: {id:''}},
        'update':     {method:'PUT'},
    });
});
