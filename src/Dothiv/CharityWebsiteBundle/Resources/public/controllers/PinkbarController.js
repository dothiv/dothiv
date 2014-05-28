'use strict';

angular.module('dotHIVApp.controllers').controller('PinkbarController', ['$scope', '$rootScope', '$http', 'config',
    function ($scope, $rootScope, $http, config) {

        $scope.bar = {
            'enabled': false,
            'donated': 0,
            'donated_label': '$0.00',
            'unlocked': 0,
            'unlocked_label': '$0.00',
            'percent': 0,
            'clicks': 0,
            'clicks_label': '0 Clicks',
            'increment': 0.1,
            'increment_label': '$0.10'
        };

        $scope.showfunding = false;

        $scope.toggle = function () {
            $scope.showfunding = !$scope.showfunding;
        }
        $rootScope.$on('pinkbar.toggle', $scope.toggle);

        function success(data) {
            $scope.bar = data;
            if (!$scope.bar.enabled) {
                $scope.bar.percent = 1;
                $scope.bar.unlocked_label = '';
                $scope.bar.donated_label = '';
                $scope.bar.clicks_label = '';
            }
        }

        $http({method: 'GET', url: '/' + config.locale + '/pinkbar'}).success(success);
    }
]);
