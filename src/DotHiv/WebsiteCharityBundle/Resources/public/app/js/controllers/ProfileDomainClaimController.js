'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileDomainClaimController', ['$scope', '$location', 'security', 'dothivDomainResource',
    function($scope, $location, security, dothivDomainResource) {
        // retrieve token from query parameters
        $scope.token = $location.search().token;

        // look up corresponding domain
        $scope.domain = dothivDomainResource.search(
                {"token": $scope.token},
                function() { // success
                },
                function() { // error
                    // TODO: Show error message
                }
        );

        $scope.claim = function() {
            dothivDomainResource.claim({"claimingToken": $scope.token, "username": security.state.user.username});
        };
    }
]);
