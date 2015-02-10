'use strict';

angular.module('dotHIVApp.controllers').controller('ConfigureController',
    ['$rootScope', '$scope', 'config', '$state', '$stateParams', 'error', '$timeout', 'AttachmentUploader', '$http', '$window', 'ContentBehaviour',
        function ($rootScope, $scope, config, $state, $stateParams, error, $timeout, AttachmentUploader, $http, $window, ContentBehaviour) {

            // Config stuff
            $scope.config = false;
            $scope.loadConfig = function (callback) {
                $http.get('/api/landingpage/' + $stateParams.domain)
                    .success(function (response, status, headers, config) {
                        $scope.config = response;
                        updatePreview();
                        (callback || angular.noop)(response);
                    })
                    .error(function (response, status, headers, config) {
                        error.show(response.title, response.detail);
                    });
            };

            var saveConfigSettings = function (callback) {
                $http({
                    method: 'PATCH',
                    url: '/api/landingpage/' + $stateParams.domain,
                    data: angular.toJson($scope.config)
                })
                    .success(function (response, code, headers, request) {
                        $scope.loadConfig(callback);
                    })
                    .error(function (response, code, headers, request) {
                        if (code === 422) {
                            $scope.loadConfig(callback);
                            return;
                        }
                        error.show(response.title, response.detail);
                    })
                ;
            };

            // Fullscreen stuff
            $scope.fullscreen = false;
            $scope.$watch('fullscreen', function (newValue, oldValue) {
                if (newValue === oldValue) {
                    return;
                }
                $timeout(updateIframeSize, 100);
            });
            $scope.$watch('settings', function (newValue, oldValue) {
                if (newValue === oldValue) {
                    return;
                }
                $timeout(updateIframeSize, 100);
            });
            function updateIframeSize() {
                var headerHeight = $('body > header').outerHeight();
                var topNavHeight = $('#topnav').outerHeight();
                var windowHeight = $(window).height();
                var height = windowHeight - topNavHeight;
                if (!$scope.fullscreen) {
                    height -= headerHeight;
                    height -= $('#settings').outerHeight();
                }
                $scope.iframeStyle = {'height': height + 'px', 'top': topNavHeight + 'px'};
                var configuratorHeight = windowHeight - headerHeight;
                $scope.configuratorStyle = {'height': configuratorHeight + 'px'};
            }

            // Preview stuff
            function updatePreview() {
                if ($scope.iframeBaseUrl == null) {
                    $scope.iframeBaseUrl = $scope.iframeUrl;
                }
                var url = $scope.iframeBaseUrl
                    + '?' + (new Date() / 1000);
                if (typeof $scope.config.name !== "undefined") {
                    url += '&name=' + encodeURIComponent($scope.config.name)
                }
                if (typeof $scope.config.text !== "undefined") {
                    url += '&text=' + encodeURIComponent($scope.config.text)
                }
                if (typeof $scope.config.language !== "undefined") {
                    url += '&language=' + encodeURIComponent($scope.config.language)
                }
                $scope.iframeUrl = url;
            }

            $scope.updateConfigSettings = function () {
                updatePreview();
            };

            // Finish
            $scope.finish = function () {
                saveConfigSettings(function (data) {
                    $state.transitionTo('done', $stateParams);
                });
            };

            // On load
            $scope.loadConfig();
            $timeout(updateIframeSize, 100);
        }
    ]);
