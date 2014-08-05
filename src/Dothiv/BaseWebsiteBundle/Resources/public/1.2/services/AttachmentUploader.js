'use strict';

angular.module('dotHIVApp.services').factory('AttachmentUploader', ['FileUploader', 'User', function (FileUploader, User) {

    function AttachmentUploader($scope, url) {
        this.uploader = new FileUploader({
            scope: $scope,
            url: url
        });
        if (this.isIE9()) {
            this.uploader.url = this.uploader.url + '?auth_token=' + User.getAuthToken();
        } else {
            this.uploader.headers = {
                Authorization: 'Bearer ' + User.getAuthToken()
            };
        }
    }

    AttachmentUploader.prototype.isIE9 = function () {
        return parseInt((/msie (\d+)/.exec(navigator.userAgent.toLowerCase()) || [])[1], 10) == 9;
    }

    return AttachmentUploader;

}]);
