'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileDomainEditController', ['$scope', '$location', '$stateParams', 'security',
    function($scope, $location, $stateParams, security) {
        $scope.domainName = $stateParams.domainName; //TODO
    }
]);
