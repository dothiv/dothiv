'use strict';

angular.module('dotHIVApp.controllers').controller('NonProfitRegisterRegisterController', ['$scope', 'dothivAccountResource', function ($scope, dothivAccountResource) {
    $scope.errorMessage = null;
    $scope.registrationForm = {};
    $scope.progress = false;
    $scope.done = false;

    function _submit() {
        $scope.progress = true;
        $scope.errorMessage = null;
        dothivAccountResource.create(
            $scope.registrationForm,
            function () { // success
                $scope.done = true;
                $scope.progress = false;
            },
            function (response) { // error
                $scope.errorMessage = response.statusText;
                $scope.progress = false;
            }
        );
    }

    $scope.submit = _submit;
}]);
