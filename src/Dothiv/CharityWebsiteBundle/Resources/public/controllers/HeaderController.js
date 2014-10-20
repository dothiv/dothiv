'use strict';

angular.module('dotHIVApp.controllers').controller('HeaderController', ['$scope',
    function ($scope) {
        $scope.loggedIn = false;
        $scope.isAuthenticated = function () {
            return $scope.loggedIn;
        }

        $scope.login = function () {
        }

        $scope.logout = function () {
        }
    }
]);
