'use strict';

angular.module('dotHIVApp.controllers').controller('HeaderController', ['$scope', '$state', 'locale',
    function($scope, $state, locale) {
        // make state information available
        $scope.state = $state;    
    
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

