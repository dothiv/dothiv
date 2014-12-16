'use strict';

angular.module('dotHIVApp.controllers').controller('DoneController', ['$scope', '$state', '$stateParams', 'OrderModel', function ($scope, $state, $stateParams, OrderModel) {
    if (!OrderModel.isDone()) {
        $state.transitionTo('lookupform', {"locale": $stateParams.locale});
    }
    $scope.domain = $stateParams.domain;
}]);
