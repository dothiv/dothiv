'use strict';

angular.module('dotHIVApp.controllers').controller('Configure4lifeController', ['$scope', 'OrderModel', '$state', '$stateParams', function ($scope, OrderModel, $state, $stateParams) {
    if (OrderModel.isDone() || !OrderModel.isAvailable()) {
        $state.transitionTo('lookupform', {"locale": $stateParams.locale});
        return;
    }
    if ($stateParams.domain.search('4life') < 0) {
        $state.transitionTo('configure', {"locale": $stateParams.locale, "domain": $stateParams.domain});
        return;
    }
    $scope.order = OrderModel;
    $scope.domain = $stateParams.domain;
    $scope.submit = function () {
        $state.transitionTo('checkout', {"locale": $stateParams.locale, "domain": $scope.domain})
    };
}]);
