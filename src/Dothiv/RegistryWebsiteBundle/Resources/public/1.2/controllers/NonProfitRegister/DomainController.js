'use strict';

angular.module('dotHIVApp.controllers').controller('NonProfitRegisterDomainController', ['$scope', 'dothivNonProfitDomainResource', function ($scope, dothivNonProfitDomainResource) {
    $scope.errorMessage = null;
    $scope.errorExists = false;
    $scope.domainForm = {};
    $scope.registrantForm = {};
    $scope.progress = false;
    $scope.step2 = false;
    $scope.done = false;

    function _submit() {
        $scope.progress = true;
        $scope.errorMessage = null;
        dothivNonProfitDomainResource.create(
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

    /**
     * Load the request for the given domain.
     *
     * @private
     */
    function _load() {
        if ($scope.domainForm.$invalid) {
            $scope.step2 = false;
            return;
        }
        $scope.progress = true;
        dothivNonProfitDomainResource.get(
            {name: $scope.domainForm.domain},
            function () { // Success
                $scope.step2 = true;
                $scope.progress = false;
            },
            function (response) { // Error
                $scope.progress = false;
                if (response.status == 403) { // Exists from different user
                    $scope.errorExists = true;
                } else if (response.status == 404) { // No registration exists
                    $scope.step2 = true;
                } else {
                    $scope.errorMessage = response.statusText;
                }
            }
        );
    }

    $scope.submit = _submit;
    $scope.load = _load;
}]);
