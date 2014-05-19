'use strict';

angular.module('dotHIVApp.directives').directive('dhVoteItem', function() {
    return {
        restrict: 'E',
        scope: {
            vote: '=vote'
        },
        replace: true,
        template:
            '<div class="row">' +
                '<div class="span12 profile-summary-item">' +
                    '<div class="profile-summary-item-well">' +
                        '<h3 ng-bind="domain.name"></h3>' +
                        '<div class="profile-summary-item-lower">' +
                            '<a href="" class="pull-right">Zum Projekt<div class="item-arrow"></div></a>' +
                            '<div class="pull-left item-icon"></div>' +
                            '<span>2344 Votes&nbsp;&nbsp;|&nbsp;&nbsp;wurde umgesetzt</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>',
    };
});
