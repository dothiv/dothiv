'use strict';

angular.module('dotHIVApp.services').factory('dothivDomainResource', function($resource, dothivResourceDefaultActions) {
    return $resource('/app_dev.php/api/domains/:claims/:id', {}, {
        'get':    {method:'GET', params: {id:'@id'}},
        'query':  {method:'GET', isArray:true},
        'search': {method:'GET', params: {token:'@token'}},
        'claim':  {method:'POST', params: {claims:'claims'}}
    });
});
