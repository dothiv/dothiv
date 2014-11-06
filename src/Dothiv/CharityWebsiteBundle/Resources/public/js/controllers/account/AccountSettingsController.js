'use strict';

angular.module('dotHIVApp.controllers').controller('AccountSettingsController', ['$scope', 'security', 'User', '$http',
    function ($scope, security, User, $http) {
        $scope.user = security.user;
        $scope.error = null;
        $scope.loading = false;

        var changeEmail = function (nextStep) {
            var data = {email: $scope.new_email};
            $scope.loading = true;
            $scope.error = null;
            $http({method: 'PATCH', url: '/api/user/' + User.getHandle(), data: angular.toJson(data)})
                .success(function(data, status, headers, config) {
                    $scope.loading = false;
                    $scope.step = nextStep;
                })
                .error(function(data, status, headers, config) {
                    $scope.loading = false;
                    $scope.error = status;
                })
            ;
        };

        $scope.submit = function (nextStep) {
            switch ($scope.step) {
                case 'form':
                    changeEmail(nextStep);
                    break;
                default:
                    $scope.step = nextStep;
            }
        };
    }
]);
