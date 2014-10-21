'use strict';

angular.module('dotHIVApp', ['dotHIVApp.services', 'dotHIVApp.controllers', 'ngRoute', 'ui.router', 'colorpicker.module', 'ui.bootstrap'])
    .config(['$locationProvider', function ($locationProvider) {
        $locationProvider.hashPrefix('!');
    }])
    .config(['$interpolateProvider', function($interpolateProvider) {
        $interpolateProvider.startSymbol('%%');
        $interpolateProvider.endSymbol('%%');
    }])
    .config(['$httpProvider', function ($httpProvider) {
        $httpProvider.defaults.headers.common.Accept = "application/json";
        $httpProvider.interceptors.push('HttpLoadingInterceptor');
    }])
    // Configute routing and states
    .config(['$stateProvider', function ($stateProvider) {
        var path = document.location.pathname.split("/");
        var locale = path[1];
        var domain = path[3];
        $stateProvider
        .state('login', {
            url: '/login',
            templateUrl: '/' + locale + '/premium-configurator/' + domain + '/app/login.html'
        })
        .state('=', {
            abstract: true,
            url: '',
            template: '<div data-ui-view></div>'
        })
        .state('=.start', {
            url: '/start',
            templateUrl: '/' + locale + '/premium-configurator/' + domain + '/app/start.html'
        })
        .state('=.configure', {
            url: '/configure',
            templateUrl: '/' + locale + '/premium-configurator/' + domain + '/app/configure.html',
            controller: 'ConfigureController'
        })
        .state('=.subscription', {
            url: '/subscription',
            templateUrl: '/' + locale + '/premium-configurator/' + domain + '/app/subscription.html',
            controller: 'SubscriptionController'
        })
        ;
    }])
    .run(['$rootScope', 'security', '$state', '$window', 'ContentBehaviour', function ($rootScope, security, $state, $window, ContentBehaviour) {
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
        $rootScope.$on('$viewContentLoaded', function (event, current, previous, rejection) {
            $window.setTimeout(function() {
                ContentBehaviour.run();
            }, 0);
        });
        $state.transitionTo('=.start');
    }])
;
angular.module('dotHIVApp.services', ['dotHIVApp.controllers', 'ui.router', 'ngResource', 'ngCookies', 'angularFileUpload']);
angular.module('dotHIVApp.controllers', ['ui.bootstrap']);

