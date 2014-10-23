'use strict';

angular.module('dotHIVApp.services').factory('dothivBannerResource', ['$resource', function ($resource) {
    return $resource('/api/domain/:domain/banner', {}, {
        'get': {method: 'GET', params: {domain: '@domain'}},
        'update': {method: 'PUT', params: {domain: '@domain'}}
    });
}]);
