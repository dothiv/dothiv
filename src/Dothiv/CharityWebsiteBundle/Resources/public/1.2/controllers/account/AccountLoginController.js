'use strict';

angular.module('dotHIVApp.controllers').controller('AccountLoginController', ['$scope', 'dothivAccountResource', 'strings',
    function ($scope, dothivAccountResource, strings) {
        $scope.email = null;
        $scope.state = 'form';
        $scope.errorMessage = null;

        $scope.submit = function () {
            $scope.state = 'loading';
            $scope.errorMessage = null;
            dothivAccountResource.requestLoginLink(
                {email: $scope.email},
                function () {
                    $scope.state = 'success';
                },
                function (response) {
                    $scope.state = 'form';
                    if (response.status == 404) {
                        $scope.errorMessage = strings.error.login.notfound;
                    } else {
                        $scope.errorMessage = response.statusText;
                    }
                }
            );
        }
    }
]);
