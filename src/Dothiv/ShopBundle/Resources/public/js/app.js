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
    .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
        $stateProvider
            .state('lookupform', {
                url: '/:locale/shop/lookup',
                templateUrl: '/template/shop/lookup.html',
                controller: 'LookupFormController'
            })
            .state('lookup', {
                url: '/:locale/shop/lookup/:domain',
                templateUrl: '/template/shop/lookup-result.html',
                controller: 'LookupResultController'
            })
            .state('configure', {
                url: '/:locale/shop/configure/:domain',
                templateUrl: '/template/shop/configure.html',
                controller: 'ConfigureController'
            })
            .state('checkout', {
                url: '/:locale/shop/checkout/:domain',
                templateUrl: '/template/shop/checkout.html',
                controller: 'CheckoutController'
            })
            .state('done', {
                url: '/:locale/shop/success/:domain',
                templateUrl: '/template/shop/done.html',
                controller: 'DoneController'
            })
        ;
        var locale = document.location.pathname.split("/")[1];
        $urlRouterProvider.when('/' + locale + '/shop', '/' + locale + '/shop/lookup');
    }])
    .run(['$rootScope', '$window', 'ContentBehaviour', function ($rootScope, $window, ContentBehaviour) {
        $rootScope.$on('$viewContentLoaded', function (event, current, previous, rejection) {
            $window.setTimeout(function () {
                ContentBehaviour.run();
            }, 0);
        });
    }])
;
angular.module('dotHIVApp.services', ['dotHIVApp.controllers', 'ui.router', 'ngResource', 'ngCookies']);
angular.module('dotHIVApp.directives', []);
angular.module('dotHIVApp.filters', []);
angular.module('dotHIVApp.controllers', []);
