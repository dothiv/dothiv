'use strict';

angular.module('dotHIVApp.controllers').controller('RegisterController', ['$scope', 'dothivAccountResource', function ($scope, dothivAccountResource) {
    $scope.errorMessage = null;
    $scope.registrationForm = {};
    $scope.loading = false;
    $scope.done = false;

    function _submit() {
        $scope.loading = true;
        $scope.errorMessage = null;
        $scope.registrationForm.route = 'dothiv_payitforward_checkout';
        dothivAccountResource.create(
            $scope.registrationForm,
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
