'use strict';

angular.module('dotHIVApp.services').factory('dothivDomainResource', function($resource, dothivResourceDefaultActions) {
    return $resource('api/domains/:claims/:id/:sub', {}, {
        'get':        {method:'GET', params: {id:'@id'}},
        'query':      {method:'GET', isArray:true},
        'search':     {method:'GET', params: {token:'@token'}},
        'getBanners': {method:'GET', isArray:true, params: {id:'@id', sub:'banners'}},
        'claim':      {method:'POST', params: {claims:'claims'}},
        'save':       {method:'POST', params: {id:'@id'}},
        'update':     {method:'PUT', params: {id:'@id'}},
    });
});
