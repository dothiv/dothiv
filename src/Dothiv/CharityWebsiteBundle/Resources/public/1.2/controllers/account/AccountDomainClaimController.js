'use strict';

angular.module('dotHIVApp.controllers').controller('AccountDomainClaimController', ['$scope', '$rootScope', '$location', 'security', 'dothivDomainResource', '$state',
    function ($scope, $rootScope, $location, security, dothivDomainResource, $state) {
        // states: 0 -- enter token, 1 -- token found in URL, 2 -- success, 3 -- error

        $scope.state = 0;

        // define registration function
        $scope.register = function register(token) {
            $scope.state = 1;
            $scope.domain = dothivDomainResource.claim(
                {token: token},
                function () { // success
                    $scope.state = 2;
                    $rootScope.$emit('domain.claimed', $scope.domain);
                },
                function () { // error
                    $scope.state = 3;
                }
            );
        }

        // restart the whole process
        $scope.startover = function () {
            $scope.token = '';
            $scope.state = 0;
        }

        // switch to editor choice page for this domain
        $scope.edit = function () {
            $state.transitionTo('profile.editors', { name: $scope.domain.name });
        };
    }
]);
