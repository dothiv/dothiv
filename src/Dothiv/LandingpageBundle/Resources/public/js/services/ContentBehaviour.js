angular.module('dotHIVApp.services').factory('ContentBehaviour', ['config', '$window',
    function (config, $window) {
        return {
            run: function () {
                $('a').filter(function (index, a) {
                    var href = $(a).attr('href');
                    if (!href) {
                        return false;
                    }
                    return href.match('^(http|\/\/)') ? true : false;
                }).attr('target', '_blank');
            }
        }
    }
]);
