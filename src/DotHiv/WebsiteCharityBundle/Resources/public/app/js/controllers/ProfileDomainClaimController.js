'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileDomainClaimController', ['$scope', '$location', 'security', 'dothivDomainResource', '$state',
    function($scope, $location, security, dothivDomainResource, $state) {
        // retrieve token from query parameters
        $scope.token = $location.search().token;
        $scope.preset = !!$scope.token;
        $scope.domain = { name: "" };
        domainLookup();

        // look up corresponding domain
        function domainLookup() {
            if (!$scope.token) {
                return;
            }
            $scope.domain = dothivDomainResource.search(
                    {"token": $scope.token},
                    function() { // success
                    },
                    function() { // error
                        $scope.preset = false;
                        $scope.token = "";
                    }
            );
        }

        $scope.$watch('token', function() {
            domainLookup();
        })

        $scope.claim = function() {
            dothivDomainResource.claim({"claimingToken": $scope.token, "username": security.state.user.username}, function(d, headers) {
                $state.transitionTo('=.profile.domainedit', {'domainId': $scope.domain.name});
            }, function(a,b,c) {
                // TODO: Show error message
            });
        };
    }
]);
