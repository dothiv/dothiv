angular.module('dotHIVApp.services').factory('error', ['$rootScope', '$modal',
    function ($rootScope, $modal) {
        return {
            show: function (title, message) {
                var modalScope = $rootScope.$new();
                modalScope.title = title;
                modalScope.message = message;
                $modal.open({'templateUrl': 'error-modal.html', 'scope': modalScope, 'backdrop': 'static'});
            }
        }
    }
]);
