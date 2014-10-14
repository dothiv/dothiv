'use strict';

angular.module('dotHIVApp.controllers').controller('MainNavController', ['$scope', function ($scope) {
    $scope.collapsed = true;

    $scope.toggle = function () {
        $scope.collapsed = !$scope.collapsed;
        $("#mainmenu").trigger("open.mm");
    }
}]);
