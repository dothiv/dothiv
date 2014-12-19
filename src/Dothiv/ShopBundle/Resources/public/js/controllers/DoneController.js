'use strict';

angular.module('dotHIVApp.controllers').controller('DoneController', [
    '$scope', '$state', '$stateParams', 'OrderModel', '$location', '$anchorScroll',
    function ($scope, $state, $stateParams, OrderModel, $location, $anchorScroll) {
        if (!OrderModel.isDone()) {
            $state.transitionTo('lookupform', {"locale": $stateParams.locale});
        }
        $scope.domain = $stateParams.domain;
        $location.hash('done');
        $anchorScroll();
    }]);
