'use strict';

angular.module('dotHIVApp.controllers').controller('HomeController', ['$scope', 
    function($scope) {
    
        $scope.firmOffset = 0 ;

        $scope.firms = [
                        { 
                                'img': '/bundles/dothivcharitywebsite/images/firms/github.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivcharitywebsite/images/firms/liga01.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivcharitywebsite/images/firms/jovoto.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivcharitywebsite/images/firms/swipe.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivcharitywebsite/images/firms/github.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivcharitywebsite/images/firms/liga01.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivcharitywebsite/images/firms/jovoto.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivcharitywebsite/images/firms/swipe.jpg',
                                'url': '#!/'
                        }
                    ]
        $scope.quoteOffset = 0 ;

        $scope.quotes = [
                         {
                             'text': 'Wenn man einen Filter hat, sieht jedes Problem aus wie ein Hammer.',
                             'person': 'Komplize N'
                         },
                         {
                             'text': 'Das ist ja geil!',
                             'person': 'Komplize A'
                         },
                         {
                             'text': 'Awesome!',
                             'person': 'Komplize B'
                         },
                         {
                             'text': 'Erstma schön nen pull-Request machen...',
                             'person': 'Komplize M'
                         }
                     ];

        $scope.toggleHeader = function() {
            angular.element(document.getElementById('header')).scope().toggle();
        }
    }
]);
