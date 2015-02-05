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
        ;
    }])
    .run(['$rootScope', '$window', 'ContentBehaviour', function ($rootScope, $window, ContentBehaviour) {
        $rootScope.$on('$viewContentLoaded', function (event, current, previous, rejection) {
            $window.setTimeout(function () {
                ContentBehaviour.run();
            }, 0);
        });
    }])
;
angular.module('dotHIVApp.services', ['dotHIVApp.controllers', 'ui.router', 'ngResource', 'ngCookies', 'angularFileUpload']);
angular.module('dotHIVApp.directives', []);
angular.module('dotHIVApp.filters', []);
angular.module('dotHIVApp.controllers', []);
