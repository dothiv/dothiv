'use strict';

angular.module('dotHIVApp.directives').directive('dhCommentItem', function() {
    return {
        restrict: 'E',
        scope: {
            comment: '=comment'
        },
        replace: true,
        template:
            '<div class="row">' +
                '<div class="span12 profile-summary-item">' +
                    '<div class="profile-summary-item-well">' +
                        '<h3 ng-bind="comment.project"></h3>' +
                        '<div class="profile-summary-item-lower">' +
                            '<a class="pull-right" href="#">Zum Projekt<div class="item-arrow"></div></a>' +
                            '<div class="pull-left item-icon"></div>' +
                            '<span>"<span ng-bind="comment.text"></span>"</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>',
    };
});
