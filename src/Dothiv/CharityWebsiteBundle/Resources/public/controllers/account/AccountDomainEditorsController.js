'use strict';

angular.module('dotHIVApp.controllers').controller('AccountDomainEditorsController', ['$scope', '$state', '$stateParams', 'dothivDomainResource', 'dothivUserResource', 'User', function ($scope, $state, $stateParams, dothivDomainResource, dothivUserResource, User) {
    var domainName = $stateParams.name;
    $scope.domain = {name: domainName};

    $scope.editBasic = function () {
        $state.transitionTo('profile.editbasic', { name: $scope.domain.name });
    };

    $scope.editPremium = function () {
    };
}]);
