'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileDomainEditorsController', ['$scope', '$location', '$stateParams', 'dothivDomainResource', 'Banner',
    function($scope, $location, $stateParams, dothivDomainResource, Banner) {
        // retrieve domain id from URL parameters and get domain/banner information
        var domainId = $stateParams.domainId;
        $scope.domain = dothivDomainResource.get({"id": domainId});
    }
]);
