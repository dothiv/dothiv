'use strict';

angular.module('dotHIVApp.controllers').controller('ConfigureController', ['$rootScope', '$scope', 'dothivBannerResource', 'config', '$state', '$modal', '$timeout', 'AttachmentUploader',
    function ($rootScope, $scope, dothivBannerResource, config, $state, $modal, $timeout, AttachmentUploader) {

        $scope.fullscreen = false;
        $scope.bannerPosition = 'center';
        $scope.iframeUrl = null;
        $scope.iframeBaseUrl = null;
        $scope.iframeStyle = {'height': '600px'};
        $scope.settings = 'general';
        $scope.bannerForm = {};
        $scope.premiumBanner = {};

        var visualUploader = new AttachmentUploader($scope, '/api/premium-configurator/image');
        $scope.visualUploader = visualUploader.uploader;
        var bgUploader = new AttachmentUploader($scope, '/api/premium-configurator/image');
        $scope.bgUploader = bgUploader.uploader;
        visualUploader.uploader.onAfterAddingFile = function (item) {
            visualUploader.uploader.uploadItem(item);
        };
        bgUploader.uploader.onAfterAddingFile = function (item) {
            bgUploader.uploader.uploadItem(item);
        };
        visualUploader.uploader.onErrorItem =
            bgUploader.uploader.onErrorItem = function (item, response, status, headers) {
                var modalScope = $rootScope.$new();
                modalScope.code = status;
                $modal.open({'templateUrl': 'uploadfailed.html', 'scope': modalScope});
            };

        visualUploader.uploader.onCompleteItem = function (item, response, status, headers) {
            $scope.premiumBanner.visual = headers.location;
        };
        bgUploader.uploader.onCompleteItem = function (item, response, status, headers) {
            $scope.premiumBanner.bg = headers.location;
        };

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

        function updateIframeSize() {
            var height = $(window).height() - $('#topnav').outerHeight();
            if (!$scope.fullscreen) {
                height -= $('body > header').outerHeight();
                height -= $('#settings').outerHeight();
            }
            $scope.iframeStyle = {'height': height + 'px'};
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

        $scope.updateBannerSettings = function () {
            if (!$scope.bannerForm.$valid) {
                return;
            }
            $scope.banner = dothivBannerResource.update(
                $scope.banner,
                function () { // success
                    $scope.banner.domain = config.domain;
                    updatePreview();
                }
            );
        }
    }
]);
