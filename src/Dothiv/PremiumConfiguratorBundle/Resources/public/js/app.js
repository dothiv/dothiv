'use strict';

angular.module('dotHIVApp', ['dotHIVApp.services', 'dotHIVApp.controllers', 'ngRoute', 'ui.router'])
    .config(['$locationProvider', function ($locationProvider) {
        $locationProvider.hashPrefix('!');
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
        .state('=.payment', {
            url: '/payment',
            templateUrl: '/' + locale + '/premium-configurator/' + domain + '/app/payment.html',
            controller: 'PaymentController'
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
angular.module('dotHIVApp.controllers', []);

