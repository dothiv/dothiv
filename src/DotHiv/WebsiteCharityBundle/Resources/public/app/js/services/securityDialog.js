'use strict';

angular.module('dotHIVApp.services').factory('securityDialog', function($dialog) {
    function _showLogin() {
        $dialog.dialog({
            keyboard: true, // TODO make these values default
            backdropClick: true, // TODO make these values default
            dialogFade: true, // TODO make these values default
            backdropFade: true, // TODO make these values default
            templateUrl: '/app_dev.php/partial/login',
            controller: 'LoginDialogController'
        }).open();
    }

    return {
        showLogin: _showLogin
    };
});
