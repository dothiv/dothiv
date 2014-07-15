'use strict';

angular.module('dotHIVApp', ['ngRoute', 'dotHIVApp.services', 'dotHIVApp.controllers', 'ui.router', 'ui.bootstrap'])
    .config(['$httpProvider', function ($httpProvider) {
        $httpProvider.defaults.headers.common.Accept = "application/json";
    }])
    .config(['$locationProvider', function ($locationProvider) {
        $locationProvider.hashPrefix('!');
    }])
    // Configute routing and states
    .config(['$stateProvider', function ($stateProvider) {
        var locale = document.location.pathname.split("/")[1];
        $stateProvider
            .state('register', {
                url: '/register',
                templateUrl: '/' + locale + '/app/non-profit-register/register.html',
                controller: 'NonProfitRegisterRegisterController'
            })
            .state('login', {
                url: '/login',
                templateUrl: '/' + locale + '/app/non-profit-register/login.html',
                controller: 'NonProfitRegisterLoginController'
            })
            .state('auth', {
                url: '/auth/:handle/:auth_token',
                templateUrl: '/' + locale + '/app/non-profit-register/auth.html',
                controller: 'NonProfitRegisterAuthController'
            })
            .state('=', {
                abstract: true,
                url: '/registration',
                template: '<div data-ui-view></div>'
            })
            .state('=.domain', {
                url: '/domain',
                templateUrl: '/' + locale + '/app/non-profit-register/domain.html',
                controller: 'NonProfitRegisterDomainController'
            })
        ;
    }])
    .run(['$rootScope', 'security', '$state', function ($rootScope, security, $state) {
        $state.transitionTo('register');
        $rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
            if (toState.name.match('^=\.')) {
                // Get the current user when the application starts (in case they are still logged in from a previous session)
                security.updateUserInfo();
                security.schedule(function () {
                    if (!security.isAuthenticated()) {
                        event.preventDefault();
                        $state.transitionTo('login');
                    }
                });
            }
        });
    }])
;
angular.module('dotHIVApp.services', ['ui.router', 'dotHIVApp.controllers', 'ngResource', 'ngCookies']);
angular.module('dotHIVApp.controllers', ['angularFileUpload']);

