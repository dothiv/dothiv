'use strict';

angular.module('dotHIVApp.controllers').controller('QuoteController', ['$scope', '$http', 'config',
    function ($scope, $http, config) {

        $scope.quoteOffset = 0;

        $scope.quotes = [];

        function success(data) {
            $scope.quotes = data;
        }

        $http({method: 'GET', url: config.locale + '/content/Quote?markdown=quote'}).success(success);
    }
]);
