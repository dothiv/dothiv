'use strict';

angular.module('dotHIVApp.controllers').controller('ConfigureController', ['$scope', 'OrderModel', '$state', '$stateParams', '$location', '$anchorScroll', function ($scope, OrderModel, $state, $stateParams, $location, $anchorScroll) {
    if (OrderModel.isDone() || !OrderModel.isAvailable()) {
        $state.transitionTo('lookupform', {"locale": $stateParams.locale});
        return;
    }
    if ($stateParams.domain.search('4life') > 0) {
        $state.transitionTo('configure4life', {"locale": $stateParams.locale, "domain": $stateParams.domain});
        return;
    }
    OrderModel.step = 2;
    $scope.order = OrderModel;
    $scope.domain = $stateParams.domain;
    $scope.redirectExample = "http://www." + $scope.domain.split(".hiv").join("") + ".com/";

    $scope.updateRedirectUrl = function () {
        var input = $scope.order.redirect;
        if (!input.match(/^https?:/)) {
            $scope.order.redirect = 'http://' + input;
        }
    };

    $scope.submit = function () {
        $state.transitionTo('checkout', {"locale": $stateParams.locale, "domain": $scope.domain})
        $location.hash('shop');
        $anchorScroll();
    };
}]);
