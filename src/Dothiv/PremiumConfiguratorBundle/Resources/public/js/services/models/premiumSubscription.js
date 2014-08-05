'use strict';

angular.module('dotHIVApp.services').factory('dothivPremiumSubscription', ['$resource', function ($resource) {
    return $resource('/api/premium-configurator/:domain/subscription', {}, {
        'get': {method: 'GET', params: {domain: '@domain'}},
        'create': {method: 'PUT', params: {domain: '@domain'}}
    });
}]);
