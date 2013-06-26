'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileDomainController', ['$scope', '$location', '$state', 'security', 'dothivUserResource',
    function($scope, $location, $state, security, dothivUserResource) {
        // get personal list of domains from server
        $scope.domains = dothivUserResource.getDomains(
            {"username": security.state.user.username},
            function() { // success
            },
            function() { // error
            }
        );
    }
]);