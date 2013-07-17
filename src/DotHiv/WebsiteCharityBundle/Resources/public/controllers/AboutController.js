'use strict';

angular.module('dotHIVApp.controllers').controller('AboutController', ['$scope', '$state',
    function($scope, $state) {
        // make current state information available
        $scope.state = $state;
    }
]);
