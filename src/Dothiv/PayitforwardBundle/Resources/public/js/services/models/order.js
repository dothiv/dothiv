'use strict';

angular.module('dotHIVApp.services').factory('dothivPayitforwardOrder', ['$resource', function ($resource) {
    return $resource('/api/payitforward/order', {}, {
        'create': {method: 'PUT'}
    });
}]);
