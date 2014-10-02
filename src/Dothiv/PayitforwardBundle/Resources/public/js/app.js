'use strict';

angular.module('dotHIVApp', ['dotHIVApp.services', 'dotHIVApp.controllers', 'ngRoute', 'ui.router', 'ui.bootstrap'])
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
    }])
    .run(['$rootScope', 'security', '$state', function ($rootScope, security, $state) {
    }])
;
angular.module('dotHIVApp.services', ['dotHIVApp.controllers', 'ui.router', 'ngResource', 'ngCookies']);
angular.module('dotHIVApp.controllers', ['ui.bootstrap']);
