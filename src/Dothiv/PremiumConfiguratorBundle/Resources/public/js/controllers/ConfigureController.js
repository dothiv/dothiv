'use strict';

angular.module('dotHIVApp.controllers').controller('ConfigureController', ['$rootScope', '$scope', 'dothivBannerResource', 'dothivPremiumBannerResource', 'config', '$state', '$modal', '$timeout', 'AttachmentUploader', '$http',
    function ($rootScope, $scope, dothivBannerResource, dothivPremiumBannerResource, config, $state, $modal, $timeout, AttachmentUploader, $http) {

        $scope.fullscreen = false;
        $scope.bannerPosition = 'top';
        $scope.iframeUrl = null;
        $scope.iframeBaseUrl = null;
        $scope.iframeStyle = {};
        $scope.configuratorStyle = {};
        $scope.settings = 'forwarding';
        $scope.bannerForm = {};
        $scope.fontsForm = {};
        $scope.uploadedImages = {};
        // TODO: fetch settings from server.
        $scope.config = {
            'max_upload_size': '10MB',
            'image_size': '44x44px',
            'image_size_micro': '22x22px',
            'image_size_bg': '150x150px'
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
        var fontTypes = ['headline', 'text'];
        for (var k in fontTypes) {
            (function (fontType) {
                $scope[fontType + 'FontSelected'] = function (item, model, label) {
                    $scope.premiumBanner[fontType + 'Font'] = label;
                    $scope.fontsForm[fontType + 'Font'] = item;
                    $scope.fontsForm[fontType + 'FontLabel'] = label;
                };
            })(fontTypes[k]);
        }
        // Load fonts
        $scope.fonts = [];
        $scope.fontsLoaded = $http.get('/bundles/dothivpremiumconfigurator/data/googlefonts.json').success(function (data, status, headers, config) {
            $scope.fonts = data.items;
        });

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
                    $scope.banner.redirect_url = 'http://' + config.domain.split('.hiv').join('.com');
                    $scope.banner.language = 'en';
                    $scope.banner.position_first = 'top';
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
                // Set selected fonts
                $scope.fontsLoaded.then(function () {
                    for (var k in fontTypes) {
                        (function (fontType) {
                            var selectedFont = $scope.premiumBanner[fontType + 'Font'];
                            if (typeof selectedFont == "undefined") {
                                return;
                            }
                            // Family
                            for (var f in $scope.fonts) {
                                if ($scope.fonts[f].family == selectedFont) {
                                    $scope.fontsForm[fontType + 'FontLabel'] = selectedFont;
                                    $scope.fontsForm[fontType + 'Font'] = $scope.fonts[f];
                                    break;
                                }
                            }
                        })(fontTypes[k]);
                    }
                });
                // Set uploaded images
                for (var k in uploaders) {
                    (function (upload) {
                        var attachment = $scope.premiumBanner[upload];
                        if (typeof attachment == "undefined") {
                            return;
                        }
                        $scope.uploadedImages[upload] = $scope.premiumBanner['@context'][upload].url;
                    })(uploaders[k]);
                }

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
            $scope.iframeUrl = $scope.iframeBaseUrl + '?dothivclickcounter[position]=' + $scope.bannerPosition + '&' + (new Date() / 1000);
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
        $scope.$watch('bannerForm.enableSubsequentVisit', function(enableSubsequentVisit) {
            if (!enableSubsequentVisit) {
                $scope.banner.position = null;
            }
        });
        $scope.$watch('banner.position', function(position) {
            if (position == 'invisible') {
                $modal.open({'templateUrl': 'positionInvisibleWarning.html'});
            } else if (position == null) {
                $scope.bannerPosition = $scope.banner.position_first;
            } else {
                $scope.bannerPosition = position;
            }
        });
        $scope.$watch('banner.position_first', function(position) {
            $scope.bannerPosition = position;
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

        $scope.finish = function () {
            var modalScope = $rootScope.$new();
            modalScope.domain = config.domain;
            modalScope.redirect_url = $scope.banner.redirect_url;
            $modal.open({'templateUrl': 'code.html', 'scope': modalScope});
        }
    }
]);
