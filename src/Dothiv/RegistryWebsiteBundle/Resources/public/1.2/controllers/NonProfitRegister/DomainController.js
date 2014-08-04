'use strict';

angular.module('dotHIVApp.controllers').controller('NonProfitRegisterDomainController', ['$scope', 'dothivNonProfitDomainResource', 'security', 'User', 'FileUploader', 'idn', function ($scope, dothivNonProfitDomainResource, security, User, FileUploader, idn) {
    $scope.errorMessage = null;
    $scope.uploadError = null;
    $scope.errorExists = false;
    $scope.domain = null;
    $scope.domainForm = {};
    $scope.registrantForm = {};
    $scope.registrant = {
        personFirstname: security.user.firstname,
        personSurname: security.user.surname,
        personEmail: security.user.email
    };
    $scope.upload = null;
    $scope.progress = false;
    $scope.step2 = false;
    $scope.done = false;
    $scope.orgFields = [
        'awareness raising',
        'prevention',
        'care',
        'mother-to-child transmission',
        'STIs',
        'stigma and discrimination',
        'IT for Good',
        'other'
    ];
    $scope.countries = [
        'Afghanistan (‫افغانستان‬‎)',
        'Åland Islands (Åland)',
        'Albania (Shqipëri)',
        'Algeria (‫الجزائر‬‎)',
        'American Samoa',
        'Andorra',
        'Angola',
        'Anguilla',
        'Antarctica',
        'Antigua and Barbuda',
        'Argentina',
        'Armenia (Հայաստան)',
        'Aruba',
        'Ascension Island',
        'Australia',
        'Austria (Österreich)',
        'Azerbaijan (Azərbaycan)',
        'Bahamas',
        'Bahrain (‫البحرين‬‎)',
        'Bangladesh (বাংলাদেশ)',
        'Barbados',
        'Belarus (Беларусь)',
        'Belgium (België)',
        'Belize',
        'Benin (Bénin)',
        'Bermuda',
        'Bhutan (འབྲུག)',
        'Bolivia',
        'Bosnia and Herzegovina (Босна и Херцеговина)',
        'Botswana',
        'Bouvet Island',
        'Brazil (Brasil)',
        'British Indian Ocean Territory',
        'British Virgin Islands',
        'Brunei',
        'Bulgaria (България)',
        'Burkina Faso',
        'Burundi (Uburundi)',
        'Cambodia (កម្ពុជា)',
        'Cameroon (Cameroun)',
        'Canada',
        'Canary Islands (islas Canarias)',
        'Cape Verde (Kabu Verdi)',
        'Caribbean Netherlands',
        'Cayman Islands',
        'Central African Republic (République centrafricaine)',
        'Ceuta and Melilla (Ceuta y Melilla)',
        'Chad (Tchad)',
        'Chile',
        'China (中国)',
        'Christmas Island',
        'Clipperton Island',
        'Cocos (Keeling) Islands (Kepulauan Cocos (Keeling))',
        'Colombia',
        'Comoros (‫جزر القمر‬‎)',
        'Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)',
        'Congo (Republic) (Congo-Brazzaville)',
        'Cook Islands',
        'Costa Rica',
        'Côte d’Ivoire',
        'Croatia (Hrvatska)',
        'Cuba',
        'Curaçao',
        'Cyprus (Κύπρος)',
        'Czech Republic (Česká republika)',
        'Denmark (Danmark)',
        'Diego Garcia',
        'Djibouti',
        'Dominica',
        'Dominican Republic (República Dominicana)',
        'Ecuador',
        'Egypt (‫مصر‬‎)',
        'El Salvador',
        'Equatorial Guinea (Guinea Ecuatorial)',
        'Eritrea',
        'Estonia (Eesti)',
        'Ethiopia',
        'Falkland Islands (Islas Malvinas)',
        'Faroe Islands (Føroyar)',
        'Fiji',
        'Finland (Suomi)',
        'France',
        'French Guiana (Guyane française)',
        'French Polynesia (Polynésie française)',
        'French Southern Territories (Terres australes françaises)',
        'Gabon',
        'Gambia',
        'Georgia (საქართველო)',
        'Germany (Deutschland)',
        'Ghana (Gaana)',
        'Gibraltar',
        'Greece (Ελλάδα)',
        'Greenland (Kalaallit Nunaat)',
        'Grenada',
        'Guadeloupe',
        'Guam',
        'Guatemala',
        'Guernsey',
        'Guinea (Guinée)',
        'Guinea-Bissau (Guiné Bissau)',
        'Guyana',
        'Haiti',
        'Heard &amp; McDonald Islands',
        'Honduras',
        'Hong Kong (香港)',
        'Hungary (Magyarország)',
        'Iceland (Ísland)',
        'India (भारत)',
        'Indonesia',
        'Iran (‫ایران‬‎)',
        'Iraq (‫العراق‬‎)',
        'Ireland',
        'Isle of Man',
        'Israel (‫ישראל‬‎)',
        'Italy (Italia)',
        'Jamaica',
        'Japan (日本)',
        'Jersey',
        'Jordan (‫الأردن‬‎)',
        'Kazakhstan (Казахстан)',
        'Kenya',
        'Kiribati',
        'Kosovo (Kosovë)',
        'Kuwait (‫الكويت‬‎)',
        'Kyrgyzstan (Кыргызстан)',
        'Laos (ລາວ)',
        'Latvia (Latvija)',
        'Lebanon (‫لبنان‬‎)',
        'Lesotho',
        'Liberia',
        'Libya (‫ليبيا‬‎)',
        'Liechtenstein',
        'Lithuania (Lietuva)',
        'Luxembourg',
        'Macau (澳門)',
        'Macedonia (FYROM) (Македонија)',
        'Madagascar (Madagasikara)',
        'Malawi',
        'Malaysia',
        'Maldives',
        'Mali',
        'Malta',
        'Marshall Islands',
        'Martinique',
        'Mauritania (‫موريتانيا‬‎)',
        'Mauritius (Moris)',
        'Mayotte',
        'Mexico (México)',
        'Micronesia',
        'Moldova (Republica Moldova)',
        'Monaco',
        'Mongolia (Монгол)',
        'Montenegro (Crna Gora)',
        'Montserrat',
        'Morocco (‫المغرب‬‎)',
        'Mozambique (Moçambique)',
        'Myanmar (Burma) (မြန်မာ)',
        'Namibia (Namibië)',
        'Nauru',
        'Nepal (नेपाल)',
        'Netherlands (Nederland)',
        'New Caledonia (Nouvelle-Calédonie)',
        'New Zealand',
        'Nicaragua',
        'Niger (Nijar)',
        'Nigeria',
        'Niue',
        'Norfolk Island',
        'Northern Mariana Islands',
        'North Korea (조선 민주주의 인민 공화국)',
        'Norway (Norge)',
        'Oman (‫عُمان‬‎)',
        'Pakistan (‫پاکستان‬‎)',
        'Palau',
        'Palestine (‫فلسطين‬‎)',
        'Panama (Panamá)',
        'Papua New Guinea',
        'Paraguay',
        'Peru (Perú)',
        'Philippines',
        'Pitcairn Islands',
        'Poland (Polska)',
        'Portugal',
        'Puerto Rico',
        'Qatar (‫قطر‬‎)',
        'Réunion (La Réunion)',
        'Romania (România)',
        'Russia (Россия)',
        'Rwanda',
        'Saint Barthélemy (Saint-Barthélemy)',
        'Saint Helena',
        'Saint Kitts and Nevis',
        'Saint Lucia',
        'Saint Martin (Saint-Martin (partie française))',
        'Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)',
        'Samoa',
        'San Marino',
        'São Tomé and Príncipe (São Tomé e Príncipe)',
        'Saudi Arabia (‫المملكة العربية السعودية‬‎)',
        'Senegal (Sénégal)',
        'Serbia (Србија)',
        'Seychelles',
        'Sierra Leone',
        'Singapore',
        'Sint Maarten',
        'Slovakia (Slovensko)',
        'Slovenia (Slovenija)',
        'Solomon Islands',
        'Somalia (Soomaaliya)',
        'South Africa',
        'South Georgia &amp; South Sandwich Islands',
        'South Korea (대한민국)',
        'South Sudan (‫جنوب السودان‬‎)',
        'Spain (España)',
        'Sri Lanka (ශ්‍රී ලංකාව)',
        'St. Vincent &amp; Grenadines',
        'Sudan (‫السودان‬‎)',
        'Suriname',
        'Svalbard and Jan Mayen (Svalbard og Jan Mayen)',
        'Swaziland',
        'Sweden (Sverige)',
        'Switzerland (Schweiz)',
        'Syria (‫سوريا‬‎)',
        'Taiwan (台灣)',
        'Tajikistan',
        'Tanzania',
        'Thailand (ไทย)',
        'Timor-Leste',
        'Togo',
        'Tokelau',
        'Tonga',
        'Trinidad and Tobago',
        'Tristan da Cunha',
        'Tunisia (‫تونس‬‎)',
        'Turkey (Türkiye)',
        'Turkmenistan',
        'Turks and Caicos Islands',
        'Tuvalu',
        'U.S. Outlying Islands',
        'U.S. Virgin Islands',
        'Uganda',
        'Ukraine (Україна)',
        'United Arab Emirates (‫الإمارات العربية المتحدة‬‎)',
        'United Kingdom',
        'United States',
        'Uruguay',
        'Uzbekistan (Oʻzbekiston)',
        'Vanuatu',
        'Vatican City (Città del Vaticano)',
        'Venezuela',
        'Vietnam (Việt Nam)',
        'Wallis and Futuna',
        'Western Sahara (‫الصحراء الغربية‬‎)',
        'Yemen (‫اليمن‬‎)',
        'Zambia',
        'Zimbabwe'
    ];

    function isIE9() {
        return parseInt((/msie (\d+)/.exec(navigator.userAgent.toLowerCase()) || [])[1], 10) == 9;
    }

    var uploader = $scope.uploader = new FileUploader({
        scope: $scope,
        url: '/api/attachment'
    });
    if (isIE9()) {
        uploader.url = uploader.url + '?auth_token=' + User.getAuthToken();
    } else {
        uploader.headers = {
            Authorization: 'Bearer ' + User.getAuthToken()
        };
    }

    uploader.onAfterAddingFile = function (item) {
        uploader.uploadItem(item);
    };

    uploader.onCompleteItem = function (item, response, status, headers) {
        if (isIE9()) {
            $scope.registrant.proof = response;
        } else {
            $scope.registrant.proof = response.handle;
        }
        $scope.upload = item;
    };

    function _submit() {
        $scope.progress = true;
        $scope.errorMessage = null;
        $scope.registrant.name = idn.toASCII($scope.domain);
        dothivNonProfitDomainResource.update(
            $scope.registrant,
            function () { // success
                $scope.done = true;
                $scope.progress = false;
            },
            function (response) { // error
                $scope.errorMessage = response.statusText;
                $scope.progress = false;
            }
        );
    }

    /**
     * Load the request for the given domain.
     *
     * @private
     */
    function _load() {
        if ($scope.domainForm.$invalid) {
            $scope.step2 = false;
            return;
        }
        $scope.progress = true;
        dothivNonProfitDomainResource.get(
            {name: idn.toASCII($scope.domain)},
            function () { // Success
                $scope.step2 = true;
                $scope.progress = false;
            },
            function (response) { // Error
                $scope.progress = false;
                if (response.status == 403) { // Exists from different user
                    $scope.errorExists = true;
                } else if (response.status == 404) { // No registration exists
                    $scope.step2 = true;
                } else {
                    $scope.errorMessage = response.statusText;
                }
            }
        );
    }

    $scope.submit = _submit;
    $scope.load = _load;
}]);
