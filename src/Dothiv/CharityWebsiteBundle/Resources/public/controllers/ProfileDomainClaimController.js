'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileDomainClaimController', ['$scope', '$location', 'security', 'dothivDomainResource', '$state',
    function($scope, $location, security, dothivDomainResource, $state) {
        // states: 0 -- enter token, 1 -- token found in URL, 2 -- success, 3 -- error

        // define registration function
        $scope.register = function register(token) {
            $scope.state = 1;
            $scope.domain = dothivDomainResource.search(
                {"token": token},
                function() { // success
                    dothivDomainResource.claim({"claimingToken": token, "username": security.state.user.username}, function(d, headers) {
                        $scope.state = 2;
                        //$state.transitionTo('=.profile.domain.editors', {'domainId': $scope.domain.id});
                    }, function(a,b,c) {
                        $scope.state = 3;
                    });
                },
                function() { // error
                    $scope.state = 3;
                }
            );
        }

        // retrieve token from query parameters
        $scope.state = !!$location.search().token ? 1 : 0;
        if ($scope.state == 1) {
            $scope.register($location.search().token);
        }

        // restart the whole process
        $scope.startover = function() {
            $scope.token = '';
            $scope.state = 0;
        }

        // switch to editor choice page for this domain
        $scope.edit = function() {
            $state.transitionTo('=.profile.domain.editors', { domainId: $scope.domain.id });
        };
    }
]);
