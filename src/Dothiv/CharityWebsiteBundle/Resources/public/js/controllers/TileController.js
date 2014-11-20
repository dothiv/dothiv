'use strict';

angular.module('dotHIVApp.controllers').controller('TileController', ['$scope',
    function ($scope) {
        $scope.opened = false;

        $scope.open = function () {
            $scope.opened = true;
        };

        $scope.close = function () {
            $scope.opened = false;
        };
    }
]);
