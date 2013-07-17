'use strict';

angular.module('dotHIVApp.controllers').controller('AboutController', ['$scope', '$state', '$location', '$anchorScroll',
    function($scope, $state, $location, $anchorScroll) {
        // make current state information available
        $scope.state = $state;
        
        // TODO: this does not work when changing templates!
        $scope.scrollTo = function(id) {
            $location.hash(id);
            $anchorScroll();
         }
    }
]);
