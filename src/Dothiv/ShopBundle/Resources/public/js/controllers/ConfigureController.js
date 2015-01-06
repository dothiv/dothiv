'use strict';

angular.module('dotHIVApp.controllers').controller('ConfigureController', ['$scope', 'OrderModel', '$state', '$stateParams', function ($scope, OrderModel, $state, $stateParams) {
    if (OrderModel.isDone() || !OrderModel.isAvailable()) {
        $state.transitionTo('lookupform', {"locale": $stateParams.locale});
        return;
    }
    if ($stateParams.domain.search('4life') > 0) {
        $state.transitionTo('configure4life', {"locale": $stateParams.locale, "domain": $stateParams.domain});
        return;
    }
    $scope.order = OrderModel;
    $scope.domain = $stateParams.domain;
    $scope.redirectExample = "http://www." + $scope.domain.split(".hiv").join("") + ".com/";
    $scope.submit = function () {
        $state.transitionTo('checkout', {"locale": $stateParams.locale, "domain": $scope.domain})
    };
}]);
