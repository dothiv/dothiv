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

                // Find source code, add copy-to-clipboard button
                $('pre code').each(function (index, code) {
                    var code = $(code);
                    var pre = code.parent();
                    if (!pre.hasClass('clipboard')) {
                        var button = $('<button class="clipboard" title="' + config.strings.copy_to_clipboard + '"></button>').appendTo(pre);
                        pre.addClass('clipboard');
                        var client = new ZeroClipboard(button);
                        client.on("copy", function (event) {
                            var clipboard = event.clipboardData;
                            clipboard.setData("text/plain", code.text());
                            button.addClass('copied');
                            $window.setTimeout(function () {
                                button.removeClass('copied');
                            }, 1000);
                        });
                    }
                });
            }

        }
    }
]);
