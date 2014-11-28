'use strict';

angular.module('dotHIVApp.controllers').controller('TilesController', ['$scope', '$rootScope',
    function ($scope, $rootScope) {
        $scope.money = 0;
        $scope.goal = 0;
        $scope.increment = 0;
        $scope.clicks = 0;
        $scope.price = 0;
        $rootScope.$on('pinkbar.data', function (event, data) {
            for (var k in data) {
                $scope.money = data.unlocked_label;
                $scope.increment = data.increment_label;
                $scope.goal = data.goal_label;
                $scope.clicks = data.clicks_label;
                $scope.price = data.price_label;
            }
        });
    }
]);
