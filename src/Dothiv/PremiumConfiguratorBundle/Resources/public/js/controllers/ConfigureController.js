'use strict';

angular.module('dotHIVApp.controllers').controller('ConfigureController', ['$rootScope', '$scope', 'dothivBannerResource', 'dothivPremiumBannerResource', 'config', '$state', '$modal', '$timeout', 'AttachmentUploader', '$http',
    function ($rootScope, $scope, dothivBannerResource, dothivPremiumBannerResource, config, $state, $modal, $timeout, AttachmentUploader, $http) {

        $scope.fullscreen = false;
        $scope.bannerPosition = 'center';
        $scope.iframeUrl = null;
        $scope.iframeBaseUrl = null;
        $scope.iframeStyle = {};
        $scope.configuratorStyle = {};
        $scope.settings = 'general';
        $scope.bannerForm = {};
        $scope.fontsForm = {};
        $scope.uploadedImages = {};
        // TODO: fetch settings from server.
        $scope.config = {
            'max_upload_size': '10MB',
            'image_size': '100x100px'
        };

        // Image Uploaders
        var uploaders = ['visual', 'bg', 'extrasVisual'];
        for (var k in uploaders) {
            (function (type) {
                var uploader = new AttachmentUploader($scope, '/api/premium-configurator/image');
                $scope[type + 'Uploader'] = uploader.uploader;
                uploader.uploader.onAfterAddingFile = function (item) {
                    uploader.uploader.uploadItem(item);
                };
                uploader.uploader.onErrorItem = function (item, response, status, headers) {
                    var modalScope = $rootScope.$new();
                    modalScope.code = status;
                    $modal.open({'templateUrl': 'uploadfailed.html', 'scope': modalScope});
                };
                uploader.uploader.onCompleteItem = function (item, response, status, headers) {
                    if (uploader.isIE9()) {
                        $scope.premiumBanner[type] = response;
                    } else {
                        $scope.premiumBanner[type] = response.handle;
                    }
                    $scope.uploadedImages[type] = headers.location;
                    $scope.updatePremiumSettings();
                };
            })(uploaders[k]);
        }

        // Fonts
        $scope.fonts = [];
        $scope.fontsLoaded = $http.get('/bundles/dothivpremiumconfigurator/data/googlefonts.json').success(function (data, status, headers, config) {
            $scope.fonts = data.items;
        });
        $scope.headlineFontSelected = function (item, model, label) {
            $scope.premiumBanner.headlineFont = label;
            $scope.fontsForm.headlineFont = item;
        };
        $scope.textFontSelected = function (item, model, label) {
            $scope.premiumBanner.textFont = label;
            $scope.fontsForm.textFont = item;
        };

        // Fetch the banner
        $scope.banner = dothivBannerResource.get(
            {'domain': config.domain},
            function () { // success
                $scope.banner.domain = config.domain;
            },
            function (response) { // error
                if (response.status == 403) {
                    // User not allowed for this domain.
                    $modal.open({'templateUrl': 'forbidden.html', 'backdrop': 'static'});
                }
                if (response.status == 404) {
                    $scope.banner.redirect_url = null;
                    $scope.banner.language = 'de';
                    $scope.banner.position_first = 'center';
                    $scope.banner.position = 'top';
                    $scope.banner.domain = config.domain;
                }
            }
        );
        $scope.updateBannerSettings = function () {
            if (!$scope.bannerForm.$valid) {
                return;
            }
            $scope.banner = dothivBannerResource.update(
                $scope.banner,
                function () { // success
                    $scope.banner.domain = config.domain;
                    updatePreview();
                },
                function (response) { // error
                    var modalScope = $rootScope.$new();
                    modalScope.code = response.status;
                    $modal.open({'templateUrl': 'updatefailed.html', 'scope': modalScope});
                }
            );
        };

        // Fetch the premium banner
        $scope.premiumBanner = dothivPremiumBannerResource.get(
            {'domain': config.domain},
            function () { // success
                $scope.premiumBanner.domain = config.domain;
            },
            function (response) { // error
                if (response.status == 403) {
                    // User not allowed for this domain.
                    $modal.open({'templateUrl': 'forbidden.html', 'backdrop': 'static'});
                }
                if (response.status == 404) {
                    $scope.premiumBanner.bgColor = '#f7f7f7';
                    $scope.premiumBanner.fontColor = '#333';
                    $scope.premiumBanner.barColor = '#e00073';
                    $scope.premiumBanner.domain = config.domain;
                }
            }
        );
        $scope.updatePremiumSettings = function () {
            $scope.premiumBanner = dothivPremiumBannerResource.update(
                $scope.premiumBanner,
                function () { // success
                    $scope.premiumBanner.domain = config.domain;
                    updatePreview();
                },
                function (response) { // error
                    var modalScope = $rootScope.$new();
                    modalScope.code = response.status;
                    $modal.open({'templateUrl': 'updatefailed.html', 'scope': modalScope});
                }
            );
        }

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

        function updatePreview() {
            if ($scope.iframeBaseUrl == null) {
                $scope.iframeBaseUrl = $scope.iframeUrl;
            }
            $scope.iframeUrl = $scope.iframeBaseUrl + '?position=' + $scope.bannerPosition + '&' + (new Date() / 1000);
        }

        $scope.$watch('bannerPosition', function () {
            updatePreview();
        });
        $scope.$watch('fullscreen', function () {
            $timeout(updateIframeSize, 100);
        });
        $scope.$watch('settings', function () {
            $timeout(updateIframeSize, 100);
        });
        $scope.$watch('showExtras', function () {
            $timeout(updateIframeSize, 100);
        });

        $scope.clearExtras = function () {
            for (var k in $scope.premiumBanner) {
                if (k.substr(0, 5) == 'extra') {
                    $scope.premiumBanner[k] = null;
                }
            }
            for (var k in $scope.uploadedImages) {
                if (k.substr(0, 5) == 'extra') {
                    $scope.uploadedImages[k] = null;
                }
            }
        };
    }
]);
