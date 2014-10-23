'use strict';

angular.module('dotHIVApp.services').factory('dothivNonProfitDomainResource', ['$resource', function ($resource) {
    return $resource('/api/nonprofit/:name', {}, {
        'update': {method: 'PUT', params: {name: '@name'}},
        'get': {method: 'GET', params: {name: '@name'}}
    });
}]);
