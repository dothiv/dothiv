'use strict';

$(document).ready(function () {
    if ($(window).width() < 768) {
        $("#mainmenu").mmenu({
            "classes": "mm-white",
            "offCanvas": {
                "zposition": "front"
            }
        });
    }
});
