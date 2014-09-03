'use strict';

angular.module('dotHIVApp', ['dotHIVApp.services', 'dotHIVApp.filters', 'dotHIVApp.controllers', 'ui.bootstrap']);
angular.module('dotHIVApp.services', ['dotHIVApp.controllers']);
angular.module('dotHIVApp.controllers', ['ui.bootstrap']);
angular.module('dotHIVApp.directives', []);
angular.module('dotHIVApp.filters', []);

// Open external links in new windows.
$(function() {
    $('a').filter(function (index, a) {
        var href = $(a).attr('href');
        if (!href) {
            return false;
        }
        return href.match('^(http|\/\/)') ? true : false;
    }).attr('target', '_blank');
});
