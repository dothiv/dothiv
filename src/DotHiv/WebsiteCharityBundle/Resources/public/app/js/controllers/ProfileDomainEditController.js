'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileDomainEditController', ['$scope', '$location', '$stateParams', 'security',
    function($scope, $location, $stateParams, security) {
        $scope.domainName = $stateParams.domainName; //TODO
        $scope.domainforwarding = 'true';
        $scope.bannerposition = 'center';
        $scope.bannersecondposition = 'upper';
        $scope.language = 'Deutsch';
        $scope.languages = {'german':'Deutsch', 'english':'Englisch', 'spanish':'Spanisch', 'latin':'Latein'};
        $scope.formclean = true;
    }
]);
