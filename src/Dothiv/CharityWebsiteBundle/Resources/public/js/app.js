'use strict';

angular.module('dotHIVApp', ['ngRoute', 'dotHIVApp.services', 'dotHIVApp.filters', 'dotHIVApp.controllers', 'ui.router', 'ui.bootstrap'])
    .config(['$httpProvider', function ($httpProvider) {
        $httpProvider.defaults.headers.common.Accept = "application/json";
    }])
    .config(['$locationProvider', function ($locationProvider) {
        $locationProvider.hashPrefix('!');
    }])
    // Configute routing and states
    .config(['$stateProvider', function ($stateProvider) {
        var locale = document.location.pathname.split("/")[1];
        $stateProvider
            .state('login', {
                url: '',
                templateUrl: '/' + locale + '/app/account/login.html',
                controller: 'AccountLoginController'
            })
            .state('auth', {
                url: '/auth/:handle/:auth_token',
                templateUrl: '/' + locale + '/app/account/auth.html',
                controller: 'AccountAuthController'
            })
            .state('profile', {
                abstract: true,
                url: '/profile',
                template: '<div data-ui-view></div>'
            })
            .state('profile.dashboard', {
                url: '/dashboard',
                templateUrl: '/' + locale + '/app/account/profile.html',
                controller: 'AccountProfileController'
            })
            .state('profile.editors', {
                url: '/editors/:name',
                templateUrl: '/' + locale + '/app/account/domain-editors.html',
                controller: 'AccountDomainEditorsController'
            })
            .state('profile.editbasic', {
                url: '/edit/:name',
                templateUrl: '/' + locale + '/app/account/domain-basicedit.html',
                controller: 'AccountDomainBasicEditController'
            })
            .state('profile.settings', {
                url: '/settings',
                templateUrl: '/' + locale + '/app/account/settings.html',
                controller: 'AccountSettingsController'
            })
        ;
    }])
    .run(['$rootScope', 'security', '$state', '$window', 'strings', function ($rootScope, security, $state, $window, strings) {
        $rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
            if (toState.name.match('^profile\.')) {
                // Get the current user when the application starts (in case they are still logged in from a previous session)
                security.updateUserInfo();
                security.schedule(function () {
                    if (!security.isAuthenticated()) {
                        event.preventDefault();
                        $state.transitionTo('login');
                    }
                });
            } else if (toState.name.match('^login$')) {
                security.updateUserInfo();
                security.schedule(function () {
                    if (security.isAuthenticated()) {
                        $state.transitionTo('profile.dashboard');
                    }
                });
            }
        });

        // Open external links in new windows.
        $rootScope.$on('$viewContentLoaded', function (event, current, previous, rejection) {
            $window.setTimeout(function () {
                $('a').filter(function (index, a) {
                    var href = $(a).attr('href');
                    if (!href) {
                        return false;
                    }
                    return href.match('^(http|\/\/)') ? true : false;
                }).attr('target', '_blank');

                // Find source code, add copy-to-clipboard button
                $('pre code').each(function (index, code) {
                    var code = $(code);
                    var pre = code.parent();
                    if (!pre.hasClass('clipboard')) {
                        var button = $('<button class="clipboard" title="' + strings.copy_to_clipboard + '"></button>').appendTo(pre);
                        pre.addClass('clipboard');
                        var client = new ZeroClipboard(button);
                        client.on("copy", function (event) {
                            var clipboard = event.clipboardData;
                            clipboard.setData("text/plain", code.text());
                            button.addClass('copied');
                            $window.setTimeout(function () {
                                button.removeClass('copied');
                            }, 1000);
                        });
                    }
                });
            }, 0);
        });
    }])
;
angular.module('dotHIVApp.services', ['ui.router', 'dotHIVApp.controllers', 'ngResource', 'ngCookies', 'ui.bootstrap']);
angular.module('dotHIVApp.controllers', ['ui.bootstrap']);
angular.module('dotHIVApp.directives', []);
angular.module('dotHIVApp.filters', []);
