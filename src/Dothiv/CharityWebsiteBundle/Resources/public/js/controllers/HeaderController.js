'use strict';

angular.module('dotHIVApp.controllers').controller('HeaderController', ['$scope',
    function ($scope) {
        $scope.loggedIn = false;
        $scope.isAuthenticated = function () {
            return $scope.loggedIn;
        };

        $scope.login = function () {
        };

        $scope.logout = function () {
        };

        // Menu stuff
        $scope.expanded = false;
        $scope.toggle = function () {
            $scope.expanded = !$scope.expanded;
        };
        $scope.close = function () {
            $scope.expanded = false;
        };
    }
]);
