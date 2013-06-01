'use strict';

/* Controllers */

angular.module('myApp.controllers', ['http-auth-interceptor', 'ui.bootstrap', 'myApp.services']).
    controller('homeAction', ['$scope', '$http', 'authService', '$dialog', 'security', 
                      function($scope,   $http,   authService,   $dialog,   security) {

        // TODO move to a more general place
        $scope.$on('event:auth-loginRequired', function() {
            console.log('starting login');
            security.login();
          });
        $scope.$on('event:auth-loginConfirmed', function() {
            console.log('login confirmed');
          });
        
    }])
    .controller('headerController', ['$scope', 'security',
                             function($scope,   security) {
        
        $scope.isAuthenticated = function() {
            return security.isAuthenticated();
        };
        
        $scope.login = function() {
            security.login();
        };
        
        $scope.logout = function() {
            security.logout();
        }
        
    }]);