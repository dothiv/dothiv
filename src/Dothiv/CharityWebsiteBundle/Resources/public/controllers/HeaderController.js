'use strict';

angular.module('dotHIVApp.controllers').controller('HeaderController', ['$scope', '$state', 'security', 'securityDialog', '$rootScope',
    function($scope, $state, security, securityDialog, $rootScope) {
        // make state information available
        $scope.state = $state;

        $scope.urlOffset = 0;
        $scope.urls = [
                       {'name': 'www.google.hiv' },
                       {'name': 'www.facebook.hiv' },
                       {'name': 'www.twitter.hiv' },
                       {'name': 'www.web.hiv' },
                       {'name': 'www.youtube.hiv' }
                   ];

        $scope.isAuthenticated = function() {
            return security.isAuthenticated();
        };

        $scope.login = function() {
            securityDialog.showLogin('=.profile.summary');
        };

        $scope.logout = function() {
            security.logout();
        };

        $scope.security = security.state;

        $scope.bar = {
            'total': 10,
            'current': 6.43,
            'click': 29000,
        };

        $scope.showfunding = false;

        $scope.toggle = function() {
            $scope.showfunding = !$scope.showfunding;
        }
    }
]);
