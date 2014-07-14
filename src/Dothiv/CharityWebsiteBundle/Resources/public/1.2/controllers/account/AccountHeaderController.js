'use strict';

angular.module('dotHIVApp.controllers').controller('AccountHeaderController', ['$scope', '$location', '$state', 'security',
    function($scope, $location, $state, security) {
        // make user information available
        $scope.security = security;
        $scope.user = security.user;

        // make current state information available
        $scope.state = $state;

        // make logout available and redirect to home page
        $scope.logout = function() {
            security.logout();
            $state.transitionTo('login');
        };
    }
]);
