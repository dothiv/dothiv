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
            url: '/login',
            templateUrl: '/' + locale + '/app/account/login.html',
            controller: 'AccountLoginController'
        })
        .state('profile', {
            abstract: true,
            url: '/profile',
            template: '<div data-ui-view></div>'
        })
        .state('profile.dashboard', {
            url: '/dashboard',
            templateUrl: '/' + locale + '/app/account/profile.html',
            controller: 'AccountProfileController'
        })
        .state('profile.editors', {
            url: '/editors/:name',
            templateUrl: '/' + locale + '/app/account/domain-editors.html',
            controller: 'AccountDomainEditorsController'
        })
        .state('profile.editbasic', {
            url: '/edit/:name',
            templateUrl: '/' + locale + '/app/account/domain-basicedit.html',
            controller: 'AccountDomainBasicEditController'
        });
    }])
    .run(['$state', 'security', function ($state, security) {
        var hashParts = document.location.hash.split("/");
        if (hashParts[1] == "auth") {
            security.storeCredentials(hashParts[2], hashParts[3]);
            $state.transitionTo('profile.dashboard');
        }
        security.updateUserInfo();
    }]);
angular.module('dotHIVApp.services', ['ui.router', 'dotHIVApp.controllers', 'ngResource', 'ngCookies']);
angular.module('dotHIVApp.controllers', []);
angular.module('dotHIVApp.directives', []);
angular.module('dotHIVApp.filters', []);
