'use strict';

angular.module('dotHIVApp', ['ngRoute', 'dotHIVApp.services', 'dotHIVApp.filters', 'dotHIVApp.controllers', 'ui.router'])
    .config(['$httpProvider', function ($httpProvider) {
        $httpProvider.defaults.headers.common.Accept = "application/json";
    }])
    .config(['$locationProvider', function ($locationProvider) {
        $locationProvider.hashPrefix('!');
    }])
    // Configute routing and states
    .config(['$stateProvider', function ($stateProvider) {
        var locale = document.location.pathname.split("/")[1];
        $stateProvider.state('login', {
            url: 'login',
            templateUrl: '/' + locale + '/app/account/login.html',
            controller: 'AccountLoginController'
        });
        $stateProvider.state('profile', {
            url: 'profile',
            templateUrl: '/' + locale + '/app/account/profile.html',
            controller: 'AccountProfileController'
        });
    }])
    .run(['$state', 'security', function ($state, security) {
        var hashParts = document.location.hash.split("/");
        if (hashParts[1] == "auth") {
            security.storeCredentials(hashParts[2], hashParts[3]);
            $state.transitionTo('profile');
        }
        security.updateUserInfo();
    }]);
angular.module('dotHIVApp.services', ['ui.router', 'dotHIVApp.controllers', 'ngResource']);
angular.module('dotHIVApp.controllers', []);
angular.module('dotHIVApp.directives', []);
angular.module('dotHIVApp.filters', []);
