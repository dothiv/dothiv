'use strict';

angular.module('dotHIVApp.controllers').controller('AccountLoginController', ['$scope', '$state', 'security', 'dothivAccountResource', 'strings',
    function ($scope, $state, security, dothivAccountResource, strings) {
        $scope.state = 'form';
        $scope.form = 'login';
        $scope.loginErrorMessage = null;
        $scope.registerErrorMessage = null;
        $scope.security = security;
        $scope.registrationForm = {};
        $scope.loginForm = {};

        $scope.login = function () {
            $scope.state = 'loading';
            $scope.loginErrorMessage = null;
            dothivAccountResource.requestLoginLink(
                $scope.loginForm,
                function () {
                    $scope.state = 'success';
                },
                function (response) {
                    $scope.state = 'form';
                    if (response.status == 404) {
                        $scope.loginErrorMessage = strings.error.login.notfound;
                    } else {
                        $scope.loginErrorMessage = response.statusText;
                    }
                }
            );
        }

        $scope.register = function () {
            $scope.state = 'loading';
            $scope.registerErrorMessage = null;
            dothivAccountResource.create(
                $scope.registrationForm,
                function () {
                    $scope.state = 'success';
                },
                function (response) {
                    $scope.state = 'form';
                    $scope.registerErrorMessage = response.statusText;
                }
            );
        }

        security.schedule(function () {
            if (security.isAuthenticated()) {
                $state.transitionTo('profile.dashboard');
            }
        });
    }
]);
