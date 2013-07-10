'use strict';

angular.module('dotHIVApp.controllers').controller('HeaderController', ['$scope', '$state', 'security', 'securityDialog', 'locale',
    function($scope, $state, security, securityDialog, locale) {
        // make state information available
        $scope.state = $state;

        $scope.locale = locale;
        $scope.siteLanguages = {
                                'de': 'German',
                                'en': 'English'
                               };
        $scope.$watch('locale.language', function() {
            locale.set(locale.language);
        });

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
    }
]);
