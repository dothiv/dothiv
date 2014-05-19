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
            prefix: '/bundles/dothivregistrywebsite/translations/language-',
            suffix: '.json'
        });
    }])
    // Configute routing and states
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('home', {
                    url: '/',
                    templateUrl: '/bundles/dothivregistrywebsite/templates/home.html',
                    controller: 'HomeController'
                })
            .state('policies', {
                url: '/policies',
                templateUrl: '/bundles/dothivregistrywebsite/templates/policies.html'
            })
            .state('launch', {
                url: '/launch',
                templateUrl: '/bundles/dothivregistrywebsite/templates/launch.html'
            })
            .state('registrars', {
                url: '/registrars',
                templateUrl: '/bundles/dothivregistrywebsite/templates/registrars.html'
            })
            .state('registrars-list', {
                url: '/registrars/list',
                templateUrl: '/bundles/dothivregistrywebsite/templates/registrars-list.html'
            })
            .state('stats', {
                url: '/stats',
                templateUrl: '/bundles/dothivregistrywebsite/templates/stats.html'
            })
            .state('red-ribbon', {
                url: '/red-ribbon',
                templateUrl: '/bundles/dothivregistrywebsite/templates/red-ribbon.html'
            })
            .state('support-us', {
                url: '/support-us',
                templateUrl: '/bundles/dothivregistrywebsite/templates/support-us.html'
            })
            .state('registry', {
                url: '/registry',
                templateUrl: '/bundles/dothivregistrywebsite/templates/registry.html'
            })
            .state('team', {
                url: '/team',
                templateUrl: '/bundles/dothivregistrywebsite/templates/team.html'
            })
            .state('icann', {
                url: '/icann',
                templateUrl: '/bundles/dothivregistrywebsite/templates/icann.html'
            })
            .state('contact', {
                url: '/contact',
                templateUrl: '/bundles/dothivregistrywebsite/templates/contact.html'
            })
            .state('report', {
                url: '/report',
                templateUrl: '/bundles/dothivregistrywebsite/templates/report.html'
            })
            .state('imprint', {
                url: '/imprint',
                templateUrl: '/bundles/dothivregistrywebsite/templates/imprint.html'
            })
            .state('register', {
                url: '/register',
                templateUrl: '/bundles/dothivregistrywebsite/templates/register.html'
            })
            .state('register-nonprofit', {
                url: '/register/nonprofit',
                templateUrl: '/bundles/dothivregistrywebsite/templates/register-nonprofit.html'
            })
            .state('register-nonprofit-start', {
                url: '/register/nonprofit/start',
                templateUrl: '/bundles/dothivregistrywebsite/templates/register-nonprofit-form.html'
            })
            .state('whois', {
                    url: '/whois',
                    templateUrl: '/bundles/dothivregistrywebsite/templates/whois.html',
                    controller: 'WhoisController'
                })
            .state('advantage', {
                    url: '/advantage',
                    templateUrl: '/bundles/dothivregistrywebsite/templates/advantage.html'
                })
            .state('concepts', {
                    url: '/concepts',
                    templateUrl: '/bundles/dothivregistrywebsite/templates/concepts.html'
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

