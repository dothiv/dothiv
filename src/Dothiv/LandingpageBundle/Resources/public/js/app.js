'use strict';

angular.module('dotHIVApp', ['dotHIVApp.services', 'dotHIVApp.controllers', 'dotHIVApp.directives', 'ngRoute', 'ui.router', 'ui.bootstrap'])
    .config(['$locationProvider', function ($locationProvider) {
        $locationProvider.html5Mode(true);
    }])
    .config(['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol('%%');
        $interpolateProvider.endSymbol('%%');
    }])
    .config(['$httpProvider', function ($httpProvider) {
        $httpProvider.defaults.headers.common.Accept = "application/json";
        $httpProvider.interceptors.push('HttpLoadingInterceptor');
    }])
    .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider) {
        $stateProvider
            .state('start', {
                url: '/:locale/landingpage-configurator/:domain',
                templateUrl: '/template/configurator/welcome.html',
                controller: 'ArticleController'
            })
            .state('configure', {
                url: '/:locale/landingpage-configurator/:domain/configure',
                templateUrl: '/template/configurator/configure.html',
                controller: 'ConfigureController'
            })
            .state('done', {
                url: '/:locale/landingpage-configurator/:domain/done',
                templateUrl: '/template/configurator/done.html',
                controller: 'ArticleController'
            })
        ;
    }])
    .run(['$rootScope', 'security', '$window', 'ContentBehaviour', function ($rootScope, security, $window, ContentBehaviour) {
        $rootScope.$on('$viewContentLoaded', function (event, current, previous, rejection) {
            $window.setTimeout(function () {
                ContentBehaviour.run();
            }, 0);
        });
        // Get the current user when the application starts (in case they are still logged in from a previous session)
        security.updateUserInfo();
    }])
;
angular.module('dotHIVApp.services', ['dotHIVApp.controllers', 'ui.router', 'ngResource', 'ngCookies', 'angularFileUpload']);
angular.module('dotHIVApp.directives', []);
angular.module('dotHIVApp.filters', []);
angular.module('dotHIVApp.controllers', []);
