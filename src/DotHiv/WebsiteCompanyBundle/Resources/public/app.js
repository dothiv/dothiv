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
            prefix: '/bundles/dothivwebsitecompany/translations/language-',
            suffix: '.json'
        });
    }])
    // Configute routing and states
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('home', {
                    url: '/',
                    templateUrl: '/bundles/dothivwebsitecompany/templates/home.html',
                    controller: 'HomeController'
                })
            .state('policies', {
                url: '/policies',
                templateUrl: '/bundles/dothivwebsitecompany/templates/policies.html'
            })
            .state('launch', {
                url: '/launch',
                templateUrl: '/bundles/dothivwebsitecompany/templates/launch.html'
            })
            .state('registrars', {
                url: '/registrars',
                templateUrl: '/bundles/dothivwebsitecompany/templates/registrars.html'
            })
            .state('registrars-list', {
                url: '/registrars/list',
                templateUrl: '/bundles/dothivwebsitecompany/templates/registrars-list.html'
            })
            .state('stats', {
                url: '/stats',
                templateUrl: '/bundles/dothivwebsitecompany/templates/stats.html'
            })
            .state('red-ribbon', {
                url: '/red-ribbon',
                templateUrl: '/bundles/dothivwebsitecompany/templates/red-ribbon.html'
            })
            .state('register-nonprofit', {
                url: '/register/nonprofit',
                templateUrl: '/bundles/dothivwebsitecompany/templates/register-nonprofit.html'
            })
            .state('register-nonprofit-start', {
                url: '/register/nonprofit/start',
                templateUrl: '/bundles/dothivwebsitecompany/templates/register-nonprofit-form.html'
            })
            .state('whois', {
                    url: '/whois',
                    templateUrl: '/bundles/dothivwebsitecompany/templates/whois.html',
                    controller: 'WhoisController'
                })
            .state('advantage', {
                    url: '/advantage',
                    templateUrl: '/bundles/dothivwebsitecompany/templates/advantage.html'
                })
            .state('concepts', {
                    url: '/concepts',
                    templateUrl: '/bundles/dothivwebsitecompany/templates/concepts.html'
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

