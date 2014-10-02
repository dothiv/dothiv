'use strict';

angular.module('dotHIVApp.controllers').controller('WhoisController', ['$scope', 
    function($scope) {
    
        $scope.recent = [
                         { 'url': 'www.google.hiv', 'link': '#!/'},
                         { 'url': 'www.audi.hiv', 'link': '#!/'},
                         { 'url': 'www.einewahnsinnsdomain.hiv', 'link': '#!/'}
                     ]

    }
]);
