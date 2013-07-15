'use strict';

// Declare app level module which depends on filters, and services
angular.module('dotHIVApp', ['dotHIVApp.services', 'dotHIVApp.directives', 'dotHIVApp.controllers', 'ui.state', 'pascalprecht.translate'])
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
            prefix: '/bundles/websitecharity/translations/language-',
            suffix: '.json'
        });
    }])
    // Configute routing and states
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('home', {
                    url: '/',
                    templateUrl: '/bundles/websitecharity/templates/home.html',
                    controller: 'HomeController'
                })
            .state('about', {
                    abstract: true,
                    url: '/about',
                    templateUrl: '/bundles/websitecharity/templates/about/index.html',
                    //controller: 'AboutController'
                })
            .state('about.idea', {
                    url: '',
                    templateUrl: '/bundles/websitecharity/templates/about/idea.html'
                })

            /**
             * This state is useful for development use only. It provides
             * API calls to mock thirdparty API calls that are unavailable
             * during development.
             */
            .state('mock', {
                    url: '/mock',
                    templateUrl: '/bundles/websitecharity/templates/mock.html',
                    controller: 'MockController'
                })

            /**
             * This state is the parent state for all states that require the user
             * to be logged in.
             */
            .state('=', {
                    abstract: true,
                    url: '',
                    template: '<div ui-view></div>',
                })
            .state('=.profile', {
                    abstract: true,
                    url: '/profile',
                    templateUrl: '/bundles/websitecharity/templates/profile/index.html',
                    controller: 'ProfileController'
                })
            .state('=.profile.summary', {
                    url: '',
                    templateUrl: '/bundles/websitecharity/templates/profile/summary.html',
                    controller: 'ProfileSummaryController'
                })
            .state('=.profile.edit', {
                    url: '/edit',
                    templateUrl: '/bundles/websitecharity/templates/profile/edit.html',
                    controller: 'ProfileEditController'
                })
            .state('=.profile.projects', {
                    url: '/projects',
                    templateUrl: '/bundles/websitecharity/templates/profile/projects.html'
                })
            .state('=.profile.domains', {
                    url: '/domains',
                    templateUrl: '/bundles/websitecharity/templates/profile/domains.html',
                    controller: 'ProfileDomainController'
                })
            .state('=.profile.domaineditors', {
                    url: '/domains/editors/:domainId',
                    templateUrl: '/bundles/websitecharity/templates/profile/domain-editors.html',
                    controller: 'ProfileDomainEditorsController'
                })
            .state('=.profile.domainedit', {
                    url: '/domain/edit/:domainId',
                    templateUrl: '/bundles/websitecharity/templates/profile/domain-edit.html',
                    controller: 'ProfileDomainEditController'
                })
            .state('=.profile.votes', {
                    url: '/votes',
                    templateUrl: '/bundles/websitecharity/templates/profile/votes.html'
                })
            .state('=.profile.comments', {
                    url: '/comments',
                    templateUrl: '/bundles/websitecharity/templates/profile/comments.html'
                })
            .state('=.profile.domainclaim', {
                    url: '/domain/claim',
                    templateUrl: '/bundles/websitecharity/templates/profile/domain-claim.html',
                    controller: 'ProfileDomainClaimController'
                })
        }])
    .value('$anchorScroll', angular.noop) // TODO: working, but best practice?
    .run(['security', function(security) {
        // Get the current user when the application starts (in case they are still logged in from a previous session)
        security.updateUserInfo();
    }])
    .run(['$rootScope', 'security', 'securityDialog', function($rootScope, security, securityDialog) {
        $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
            security.schedule(function() {
                if (toState.name.match('^=') && !security.isAuthenticated()) {
                    event.preventDefault();
                    securityDialog.showLogin(toState.name);
                }
            });
        });
        $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
        });
        $rootScope.$on('$stateChangeError', function(event, toState, toParams, fromState, fromParams, error) {
        });
    }])
    .run(['$rootScope', 'securityDialog', function($rootScope, securityDialog) {
        $rootScope.$on('event:auth-loginRequired', function() {
            securityDialog.showLogin();
        });

        $rootScope.$on('event:auth-loginConfirmed', function() {
        });
    }]);

angular.module('dotHIVApp.services', ['ui.bootstrap', 'ui.state', 'dotHIVApp.controllers', 'ngResource']);
angular.module('dotHIVApp.controllers', ['http-auth-interceptor', 'ui.bootstrap', 'dotHIVApp.services']);
angular.module('dotHIVApp.directives', []);
