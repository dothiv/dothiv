'use strict';

angular.module('dotHIVApp.controllers').controller('HomeController', ['$scope', 
    function($scope) {
        $scope.urlOffset = 0;
        $scope.urls = [
                       {'name': 'www.google.hiv' },
                       {'name': 'www.facebook.hiv' },
                       {'name': 'www.twitter.hiv' },
                       {'name': 'www.web.hiv' },
                       {'name': 'www.youtube.hiv' }
                   ];
        
        $scope.firmOffset = 0 ;
        $scope.firms = [
                        { 
                                'img': '/bundles/dothivregistrywebsite/images/firms/github.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivregistrywebsite/images/firms/liga01.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivregistrywebsite/images/firms/jovoto.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivregistrywebsite/images/firms/swipe.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivregistrywebsite/images/firms/github.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivregistrywebsite/images/firms/liga01.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivregistrywebsite/images/firms/jovoto.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/dothivregistrywebsite/images/firms/swipe.jpg',
                                'url': '#!/'
                        }
                    ]
        
        $scope.charityOffset = 0 ;
        $scope.charity = [
                        { 
                            'img': '/bundles/dothivregistrywebsite/images/charitylogos/dah.png',
                            'url': '#!/'
                        },
                        { 
                            'img': '/bundles/dothivregistrywebsite/images/charitylogos/crl.png',
                            'url': '#!/'
                        },
                        { 
                            'img': '/bundles/dothivregistrywebsite/images/charitylogos/iev.png',
                            'url': '#!/'
                        },
                        { 
                            'img': '/bundles/dothivregistrywebsite/images/charitylogos/lr.png',
                            'url': '#!/'
                        }
                    ]
    }
]);

