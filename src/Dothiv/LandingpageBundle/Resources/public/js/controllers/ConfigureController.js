'use strict';

angular.module('dotHIVApp.controllers').controller('ConfigureController',
    ['$rootScope', '$scope', 'config', '$state', '$stateParams', 'error', '$timeout', 'AttachmentUploader', '$http', '$window', 'ContentBehaviour',
        function ($rootScope, $scope, config, $state, $stateParams, error, $timeout, AttachmentUploader, $http, $window, ContentBehaviour) {

            // Config stuff
            $scope.config = false;
            var originalConfig = false;
            $scope.loadConfig = function (callback) {
                $http.get('/api/landingpage/' + $stateParams.domain)
                    .success(function (response, status, headers, config) {
                        $scope.config = response;
                        originalConfig = angular.copy($scope.config);
                        updatePreview();
                        (callback || angular.noop)(response);
                    })
                    .error(function (response, status, headers, config) {
                        error.show(response.title, response.detail);
                    });
            };

            var saveConfigSettings = function (callback) {
                if (!$scope.textForm.$dirty) {
                    (callback || angular.noop)($scope.config);
                    return;
                }
                var newConfig = {};
                var changes = false;
                if (!angular.equals(originalConfig.name, $scope.config.name)) {
                    newConfig.name = $scope.config.name;
                    changes = true;
                }
                if (!angular.equals(originalConfig.text, $scope.config.text)) {
                    newConfig.text = $scope.config.text;
                    changes = true;
                }
                if (!changes) {
                    return;
                }
                $http({
                    method: 'PATCH',
                    url: '/api/landingpage/' + $stateParams.domain,
                    data: angular.toJson(newConfig)
                })
                    .success(function (response, code, headers, request) {
                        $scope.loadConfig(callback);
                    })
                    .error(function (response, code, headers, request) {
                        error.show(response.title, response.detail);
                    })
                ;
            };

            // Fullscreen stuff
            $scope.fullscreen = false;
            $scope.$watch('fullscreen', function () {
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
        }
    ]);
