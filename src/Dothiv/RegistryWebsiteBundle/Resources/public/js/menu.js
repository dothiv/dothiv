'use strict';

$(document).ready(function () {
    if ($(window).width() < 768) {
        $("#mainmenu").mmenu({
            "offCanvas": {
                "zposition": "front"
            }
        });
    }
});
