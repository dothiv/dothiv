'use strict';

angular.module('dotHIVApp.services').factory('dothivBannerResource', ['$resource', function ($resource) {
    return $resource('/api/domain/:domain/banner', {}, {
        'get': {method: 'GET'},
        'update': {method: 'PUT'}
    });
}]);
