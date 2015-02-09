'use strict';

angular.module('dotHIVApp.controllers').controller('ConfigureController',
    ['$rootScope', '$scope', 'config', '$state', '$modal', '$timeout', 'AttachmentUploader', '$http', '$window', 'ContentBehaviour',
        function ($rootScope, $scope, config, $state, $modal, $timeout, AttachmentUploader, $http, $window, ContentBehaviour) {

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
        }
    ]);
