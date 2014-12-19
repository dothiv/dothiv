angular.module('dotHIVApp.services').factory('ContentBehaviour', ['config', '$window',
    function (config, $window) {
        return {
            run: function () {
                // Open external links in new window
                $('a').filter(function (index, a) {
                    var href = $(a).attr('href');
                    if (!href) {
                        return false;
                    }
                    return href.match('^(http|\/\/)') ? true : false;
                }).attr('target', '_blank');

                // Open links below a .links-external in new window
                $('.links-external a').filter(function (index, a) {
                    var href = $(a).attr('href');
                    return href ? true : false;
                }).attr('target', '_blank');

                // Set value from example
                $('label code').each(function (_, el) {
                    var code = $(el);
                    var label = $(code.parentsUntil('label').parent());
                    code.click(function (ev) {
                        var input = $($('#' + $(label).attr("for")));
                        input.val(code.text());
                        var $e = angular.element(input);
                        $e.triggerHandler('input');
                    });
                });
            }
        }
    }
]);
