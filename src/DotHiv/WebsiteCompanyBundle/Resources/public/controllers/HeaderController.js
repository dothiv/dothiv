'use strict';

angular.module('dotHIVApp.controllers').controller('HeaderController', ['$scope', 'locale',
    function($scope, locale) {
        $scope.locale = locale;
        $scope.siteLanguages = {
                                'de': 'Deutsch',
                                'en': 'English',
                                'key': 'Keys only'
                               };
        
        $scope.$watch('locale.language', function() {
            locale.set(locale.language);
        });
        
        $scope.$on('localeInitialized', function() {
            $scope.finishedbooting = true;
        });
    }
]);

