'use strict';

// Declare app level module which depends on filters, and services
angular.module('dotHIVApp', ['dotHIVApp.services', 'dotHIVApp.directives', 'dotHIVApp.filters', 'dotHIVApp.controllers', 'ui.state', 'pascalprecht.translate'])
    .config(['$urlRouterProvider', function($urlRouter) {
        $urlRouter.otherwise('/');
    }])
    .config(['$locationProvider', function($locationProvider) {
        $locationProvider.hashPrefix('!');
    }])
    .config(['$interpolateProvider', function($interpolateProvider) {
        $interpolateProvider.startSymbol('[[');
        $interpolateProvider.endSymbol(']]');
    }])
    .config(['$anchorScrollProvider', function($anchorScrollProvider) {
        $anchorScrollProvider.disableAutoScrolling();
    }])
    .config(['$httpProvider', function($httpProvider) {
        $httpProvider.defaults.headers.common.Accept = "application/json";
    }])
    .config(['$translateProvider', function($translateProvider) {
        $translateProvider.useStaticFilesLoader({
            prefix: '/bundles/websitecompany/translations/language-',
            suffix: '.json'
        });
    }])
    // Configute routing and states
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('home', {
                    url: '/',
                    templateUrl: '/bundles/websitecompany/templates/home.html',
                    controller: 'HomeController'
                })
            .state('whois', {
                    url: '/whois',
                    templateUrl: '/bundles/websitecompany/templates/whois.html',
                    controller: 'WhoisController'
                })
            .state('advantage', {
                    url: '/advantage',
                    templateUrl: '/bundles/websitecompany/templates/advantage.html'
                })
            .state('concepts', {
                    url: '/concepts',
                    templateUrl: '/bundles/websitecompany/templates/concepts.html'
                })                     
        }])

    .run(['$rootScope', 'security', 'securityDialog', function($rootScope, security, securityDialog) {
        $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
        });
        $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
        });
        $rootScope.$on('$stateChangeError', function(event, toState, toParams, fromState, fromParams, error) {
        });
    }])
    .run(['$rootScope', 'securityDialog', function($rootScope, securityDialog) {
        $rootScope.$on('event:auth-loginRequired', function() {
        });

        $rootScope.$on('event:auth-loginConfirmed', function() {
        });
    }]);

angular.module('dotHIVApp.services', ['ui.bootstrap', 'ui.state', 'dotHIVApp.controllers', 'ngResource']);
angular.module('dotHIVApp.controllers', ['http-auth-interceptor', 'ui.bootstrap', 'dotHIVApp.services']);
angular.module('dotHIVApp.directives', []);
angular.module('dotHIVApp.filters', []);

