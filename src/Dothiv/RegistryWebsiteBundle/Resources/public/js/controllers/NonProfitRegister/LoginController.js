'use strict';

angular.module('dotHIVApp.controllers').controller('NonProfitRegisterLoginController', ['$scope', 'dothivAccountResource', function ($scope, dothivAccountResource) {
    $scope.errorMessage = null;
    $scope.loginForm = {};
    $scope.progress = false;
    $scope.done = false;

    function _submit() {
        $scope.progress = true;
        $scope.errorMessage = null;
        dothivAccountResource.requestLoginLink(
            $scope.loginForm,
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
