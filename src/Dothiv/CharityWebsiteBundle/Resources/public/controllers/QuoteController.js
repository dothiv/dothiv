'use strict';

angular.module('dotHIVApp.controllers').controller('QuoteController', ['$scope', '$http', 'config',
    function ($scope, $http, config) {

        $scope.visible = false;

        $scope.quoteOffset = 0;

        $scope.quotes = [];

        function success(data) {
            $scope.quotes = data;
            $scope.visible = true;
        }

        $http({method: 'GET', url: '/' + config.locale + '/content/Quote?markdown=quote'}).success(success);
    }
]);
