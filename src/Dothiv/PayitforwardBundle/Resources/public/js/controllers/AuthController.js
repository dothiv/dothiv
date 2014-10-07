'use strict';

angular.module('dotHIVApp.controllers').controller('AuthController', ['$scope', '$state', '$stateParams', 'security',
    function ($scope, $state, $stateParams, security) {
        $scope.progress = true;
        $scope.error = false;

        security.storeCredentials($stateParams.handle, $stateParams.auth_token);
        security.updateUserInfo(function (user, response) {
            if (user) {
                $state.transitionTo('=.order');
            } else {
                $scope.progress = false;
                $scope.error = true;
            }
        });
    }
]);
