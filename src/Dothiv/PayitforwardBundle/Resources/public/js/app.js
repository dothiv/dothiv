'use strict';

angular.module('dotHIVApp', ['dotHIVApp.services', 'dotHIVApp.controllers', 'dotHIVApp.directives', 'ngRoute', 'ui.router', 'ui.bootstrap'])
    .config(['$locationProvider', function ($locationProvider) {
        $locationProvider.hashPrefix('!');
    }])
    .config(['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol('%%');
        $interpolateProvider.endSymbol('%%');
    }])
    .config(['$httpProvider', function ($httpProvider) {
        $httpProvider.defaults.headers.common.Accept = "application/json";
    }])
    .config(['$stateProvider', function ($stateProvider) {
        var locale = document.location.pathname.split("/")[1];
        $stateProvider
            .state('login', {
                url: '/login',
                templateUrl: '/' + locale + '/payitforward/app/login.html',
                controller: 'LoginController'
            })
            .state('register', {
                url: '/register',
                templateUrl: '/' + locale + '/payitforward/app/register.html',
                controller: 'RegisterController'
            })
            .state('auth', {
                url: '/auth/:handle/:auth_token',
                templateUrl: '/' + locale + '/payitforward/app/auth.html',
                controller: 'AuthController'
            })
            .state('=', {
                abstract: true,
                url: '/cart',
                template: '<section data-ui-view></section>'
            })
            .state('=.order', {
                url: '/order',
                templateUrl: '/' + locale + '/payitforward/app/order.html',
                controller: 'OrderController'
            })
        ;
    }])
    .run(['$rootScope', 'security', '$state', function ($rootScope, security, $state) {
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
angular.module('dotHIVApp.services', ['dotHIVApp.controllers', 'ui.router', 'ngResource', 'ngCookies']);
angular.module('dotHIVApp.directives', []);
angular.module('dotHIVApp.controllers', ['ui.bootstrap']);
