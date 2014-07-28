'use strict';

angular.module('dotHIVApp.controllers').controller('AccountDomainClaimController', ['$scope', '$rootScope', '$location', 'security', 'dothivDomainResource', '$state', 'idn', '$http',
    function ($scope, $rootScope, $location, security, dothivDomainResource, $state, idn, $http) {
        // states: 0 -- enter token, 1 -- token found in URL, 2 -- success, 3 -- error

        $scope.state = 0;
        $scope.errorMessage = null;

        // define registration function
        $scope.register = function register(token) {
            $scope.state = 1;
            $scope.errorMessage = null;
            $scope.domain = dothivDomainResource.claim(
                {token: token},
                function () { // success
                    $scope.state = 2;
                    $rootScope.$emit('domain.claimed', $scope.domain);
                },
                function (response) { // error
                    $scope.state = 4;
                }
            );
        }

        $scope.registerNotToken = function (domain) {
            $scope.state = 1;
            $scope.errorMessage = null;
            $scope.domain = {name: domain};

            $http({method: 'PUT', url: '/api/domain/' + idn.toASCII(domain) + '/claim'}).
                success(function (data, status, headers, config) {
                    if (status == 202) {
                        $scope.state = 3;
                    } else {
                        $scope.state = 2;
                        $rootScope.$emit('domain.claimed', $scope.domain);
                    }
                }).
                error(function (data, status, headers, config, statusText) {
                    $scope.state = 4;
                    $scope.errorMessage = 'Code: ' + status;
                }
            );
        }

        // restart the whole process
        $scope.startover = function () {
            $scope.token = '';
            $scope.state = 0;
            $scope.claimform = {
                notoken: 0
            }
        }

        // switch to editor choice page for this domain
        $scope.edit = function () {
            $state.transitionTo('profile.editors', { name: $scope.domain.name });
        };
    }
]);
