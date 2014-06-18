'use strict';

angular.module('dotHIVApp.controllers').controller('PreregisterFormController', ['$scope', '$http', function ($scope, $http) {
    $scope.registration = {};
    $scope.formstate = "form";


    function success() {
        $scope.formstate = "success";
    }

    function error() {
        $scope.formstate = "error";
    }

    $scope.submit = function () {
        $scope.formstate = "sending";
        $http.post('/api/preregister', $scope.registration).success(success).error(error);
    }
}]);
