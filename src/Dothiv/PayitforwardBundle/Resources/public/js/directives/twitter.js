'use strict';
/**
 * This directives creates a twitter share button whose sharing text
 * is dynamically derived from the scope.
 *
 * Usage:
 *   <twitter url="http://payitforward.hiv/" hashtags="dotHIV" lang="en" text="tweetText"></twitter>
 *
 *   tweetText: is the property in the current scope which will be watched for changes.
 */
angular.module('dotHIVApp.directives').directive('twitter', ['$window', function ($window) {
    var directive = {};
    directive.restrict = 'E';
    directive.compile = function (element, attributes) {
        return function ($scope, element, attributes) { // link function
            $scope.$watch(
                function () {
                    return !!$window.twttr;
                },
                function (twitterIsReady) {
                    if (twitterIsReady) {
                        $scope.$watch(attributes.text, function (tweetText) {
                            // Delete the old button
                            if ($scope.twitterDirectiveButton) {
                                element.html('');
                            }

                            // Create the new button
                            $window.twttr.widgets.createShareButton(
                                attributes.url,
                                element[0],
                                {
                                    text: tweetText,
                                    hashtags: attributes.hashtags,
                                    lang: attributes.lang
                                }
                            );
                            $scope.twitterDirectiveButton = element[0];
                        });
                    }
                }
            );


        };
    };
    return directive;
}]);
