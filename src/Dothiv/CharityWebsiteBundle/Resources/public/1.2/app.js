'use strict';

angular.module('dotHIVApp', ['ngRoute', 'dotHIVApp.services', 'dotHIVApp.filters', 'dotHIVApp.controllers', 'ui.router'])
    .config(['$httpProvider', function($httpProvider) {
        $httpProvider.defaults.headers.common.Accept = "application/json";
    }])
    .config(['$locationProvider', function($locationProvider) {
        $locationProvider.hashPrefix('!');
    }])
    // Configute routing and states
    .config(['$stateProvider', function($stateProvider) {
        var locale = document.location.pathname.split("/")[1];
        $stateProvider.state('login', {
            url: 'login',
            templateUrl: '/' + locale + '/app/account/login.html',
            controller: 'AccountLoginController'
        });
    }])
    .run(['$http', 'security', 'User', function ($http, security, User) {
        User.setHandle(document.location.pathname.split("/")[3]);
        User.setAuthToken(document.location.hash.substr(2));
        $http.defaults.headers.common['Authorization'] = 'Bearer ' + User.getAuthToken();
        // document.location.hash = '';
        // Get the current user when the application starts (in case they are still logged in from a previous session)
        security.updateUserInfo();
    }]);
angular.module('dotHIVApp.services', ['ui.router', 'dotHIVApp.controllers', 'ngResource']);
angular.module('dotHIVApp.controllers', []);
angular.module('dotHIVApp.directives', []);
angular.module('dotHIVApp.filters', []);


/*

 'use strict';

 angular.module('dotHIVApp', ['dotHIVApp.services', 'dotHIVApp.filters', 'dotHIVApp.controllers', 'ui.state'])
 .config(['$locationProvider', function($locationProvider) {
 $locationProvider.hashPrefix('!');
 }])
 ;
 angular.module('dotHIVApp.services', ['ui.state', 'dotHIVApp.controllers', 'ngResource']);
 angular.module('dotHIVApp.controllers', []);
 angular.module('dotHIVApp.directives', []);
 angular.module('dotHIVApp.filters', []);

 */
