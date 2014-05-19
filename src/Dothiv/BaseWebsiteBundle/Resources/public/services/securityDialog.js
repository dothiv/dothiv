'use strict';

angular.module('dotHIVApp.services').factory('securityDialog', function($dialog, security, $state) {
    var dialogOpen = false;

    function _showLogin(targetState) {
        if (dialogOpen)
            return; // open only one dialog

        dialogOpen = true;
        $dialog.dialog({
            keyboard: true, // TODO make these values default
            backdropClick: true, // TODO make these values default
            dialogFade: true, // TODO make these values default
            backdropFade: true, // TODO make these values default
            templateUrl: '/bundles/dothivcharitywebsite/templates/login.html',
            controller: 'LoginDialogController'
        }).open().then(function(result) {
            dialogOpen = false;
            if (!security.isAuthenticated() && ($state.includes('=') || $state.current.name == '')) {
                $state.transitionTo('home');
                return;
            }
            if (security.isAuthenticated() && targetState) {
                $state.transitionTo(targetState);
                return;
            }
            $state.transitionTo($state.current.name);
        });
    }

    function _showLoginIfNecessary() {
        if (!security.isAuthenticated()) {
            _showLogin();
        }
    }

    return {
        showLogin: _showLogin,
        showLoginIfNecessary: _showLoginIfNecessary
    };
});
