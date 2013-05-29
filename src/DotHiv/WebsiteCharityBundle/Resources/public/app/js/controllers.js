'use strict';

/* Controllers */

angular.module('myApp.controllers', ['http-auth-interceptor', 'ui.bootstrap']).
    controller('MyCtrl1', ['$scope', '$http', 'authService', '$dialog', function($scope, $http, authService, $dialog) {
        $scope.$on('event:auth-loginRequired', function() {
            console.log("login required.");
            
            // Thats how it should be like:
            // show a login form and then perform the login
//            var opts = {
//                    backdrop: true,
//                    keyboard: true,
//                    backdropClick: true,
//                    template: '<h1>use templates!</h1>',
//            };
//            
//            $dialog.dialog(opts).open();
            
            console.log("sending login data ...");
            $http({
                method: 'POST',
                url: '/app_dev.php/login_check',
                data: '_username=nils&_password=test', // hard coded login data
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function() {
                console.log("login successful.");
                authService.loginConfirmed();
            });
          });
        $scope.$on('event:auth-loginConfirmed', function() {
            console.log('login confirmed');
          });

        $scope.nav = function() {
            $http.get('/app_dev.php/api/doc/').success(function(){
                // not executed before user is authenticated
                console.log("resumed http request successful completed.");
            });
        };
        
        $scope.logout = function() {
            console.log("logging out ...");
            $http.get('/app_dev.php/logout').success(function() {
                console.log("logout complete.");
            });
        }
    }])
    .controller('MyCtrl2', [function() {
    
    }]);