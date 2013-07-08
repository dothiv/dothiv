'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileController', ['$scope', '$location', '$state', 'security',
    function($scope, $location, $state, security) {
        // make user information available
        $scope.security = security.state;

        // make current state information available
        $scope.state = $state;

        // make logout available and redirect to home page
        $scope.logout = function() {
            security.logout();
        };
    }
]);
