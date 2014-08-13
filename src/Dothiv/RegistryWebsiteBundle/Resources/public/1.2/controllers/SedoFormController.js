'use strict';

angular.module('dotHIVApp.controllers').controller('SedoFormController', ['$scope', '$http', function ($scope, $http) {
    $scope.bid = {};
    $scope.success = false;
    $scope.loading = false;
    $scope.errorMessage = null;

    function success() {
        $scope.success = true;
        $scope.loading = false;
    }

    function error(response, code, headers, request) {
        $scope.errorMessage = 'Error: ' + code;
        $scope.loading = false;
    }

    $scope.submit = function () {
        $scope.errorMessage = null;
        $scope.loading = true;
        $http.post('/api/premiumbid', $scope.bid).success(success).error(error);
    }
}]);
