'use strict';

angular.module('dotHIVApp.controllers').controller('Configure4lifeController', ['$scope', 'OrderModel', '$state', '$stateParams', '$location', '$anchorScroll', function ($scope, OrderModel, $state, $stateParams, $location, $anchorScroll) {
    if (OrderModel.isDone() || !OrderModel.isAvailable()) {
        $state.transitionTo('lookupform', {"locale": $stateParams.locale});
        return;
    }
    if ($stateParams.domain.search('4life') < 0) {
        $state.transitionTo('configure', {"locale": $stateParams.locale, "domain": $stateParams.domain});
        return;
    }
    OrderModel.step = 2;
    $scope.order = OrderModel;
    $scope.domain = $stateParams.domain;
    $scope.submit = function () {
        $state.transitionTo('checkout', {"locale": $stateParams.locale, "domain": $scope.domain})
        $location.hash('shop');
        $anchorScroll();
    };
}]);
