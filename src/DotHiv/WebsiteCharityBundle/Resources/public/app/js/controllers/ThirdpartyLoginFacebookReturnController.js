'use strict';

angular.module('dotHIVApp.controllers').controller('ThirdpartyLoginFacebookReturnController', ['$scope', '$location',
    function($scope, $location) {
        // Use regex to get the facebook 'code', as $stateParams as well as $location refuse 
        // to work in this special case for some reason (as of angularjs v1.1.5 and angular router v0.0.1).
        // TODO replace with $stateParams.code or $location.search('code') if possible
        var code = ($location.absUrl().match(/(\?|&)code\=([A-Za-z0-9\_\-]+)(#|&)/) || Array(3))[2];
        if (code) {
            // login successful
            console.log("facebook login successful: " + code);
        } else {
            // login unsuccessful
            console.log("facebook login unsuccessful");
        }
    }
]);
