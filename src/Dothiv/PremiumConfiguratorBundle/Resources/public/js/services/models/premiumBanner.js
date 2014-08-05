'use strict';

angular.module('dotHIVApp.services').factory('dothivPremiumBannerResource', ['$resource', function ($resource) {
    return $resource('/api/premium-configurator/:domain/banner', {}, {
        'get': {method: 'GET', params: {domain: '@domain'}},
        'update': {method: 'PUT', params: {domain: '@domain'}}
    });
}]);
