'use strict';

angular.module('dotHIVApp.controllers').controller('LoginController', ['$scope', 'dothivAccountResource', function ($scope, dothivAccountResource) {
    $scope.errorMessage = null;
    $scope.loginForm = {};
    $scope.loading = false;
    $scope.done = false;

    function _submit() {
        $scope.loading = true;
        $scope.errorMessage = null;
        $scope.loginForm.route = 'dothiv_payitforward_checkout';
        dothivAccountResource.requestLoginLink(
            $scope.loginForm,
            function () { // success
                $scope.done = true;
                $scope.loading = false;
            },
            function (response) { // error
                $scope.errorMessage = response.statusText;
                $scope.loading = false;
            }
        );
    }

    $scope.submit = _submit;
}]);
