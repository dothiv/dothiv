'use strict';

$(document).ready(function () {
    if ((typeof window.matchMedia != 'undefined' && window.matchMedia("(max-width: 768px)").matches) || $(window).width() < 768) {
        $("#mainmenu").mmenu({
            classes: "mm-light",
            offCanvas: {
                zposition: "front"
            }
        })
            .on("opening.mm", function () {
                $("#mainmenu-overlay").removeClass("hidden");
            })
            .on("closing.mm", function () {
                $("#mainmenu-overlay").addClass("hidden");
            });
        $('div.mm-page').after('<div id="mainmenu-overlay" class="hidden"></div>');
    }
});
