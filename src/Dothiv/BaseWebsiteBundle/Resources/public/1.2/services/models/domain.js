'use strict';

angular.module('dotHIVApp.services').factory('dothivDomainResource', ['$resource', function ($resource) {
    return $resource('/api/domain/:name/:sub', {}, {
        'claim': {method: 'POST', params: {name: 'claim'}},
        'claimNotToken': {method: 'PUT', params: {name: '@name', sub: 'claim'}},
        'getBanner': {method: 'GET', params: {name: '@name', sub: 'banner'}},
        'save': {method: 'POST', params: {name: '@name'}},
        'update': {method: 'PUT', params: {name: '@name'}}
    });
}]);
