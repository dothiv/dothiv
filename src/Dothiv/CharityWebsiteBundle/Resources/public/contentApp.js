'use strict';

angular.module('dotHIVApp', ['dotHIVApp.services', 'dotHIVApp.filters', 'dotHIVApp.controllers', 'ui.bootstrap'])
    .run(['$rootScope', '$window', function ($rootScope, $window) {
        // Open external links in new windows.
        $rootScope.$on('$viewContentLoaded', function (event, current, previous, rejection) {
            $window.setTimeout(function() {
                $('a').filter(function (index, a) {
                    var href = $(a).attr('href');
                    if (!href) {
                        return false;
                    }
                    return href.match('^(http|\/\/)') ? true : false;
                }).attr('target', '_blank');
            }, 0);
        });
    }]);
angular.module('dotHIVApp.services', ['dotHIVApp.controllers']);
angular.module('dotHIVApp.controllers', ['ui.bootstrap']);
angular.module('dotHIVApp.directives', []);
angular.module('dotHIVApp.filters', []);
