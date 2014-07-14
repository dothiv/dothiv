'use strict';

angular.module('dotHIVApp.controllers').controller('AccountAuthController', ['$scope', '$state', '$stateParams', 'security',
    function ($scope, $state, $stateParams, security) {
        $scope.error = false;

        security.storeCredentials($stateParams.handle, $stateParams.auth_token);
        security.updateUserInfo(function (user, response) {
            if (user) {
                $state.transitionTo('profile.dashboard');
            } else {
                $scope.error = true;
            }
        });
    }
]);
