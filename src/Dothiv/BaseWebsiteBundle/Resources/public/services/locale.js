'use strict';

/**
 * @name dotHIVApp.services.locale
 * @requires dothivLocaleResource, $translate, $rootScope
 * 
 * @description
 * Manages the user's locale. This includes telling the server which locale to use.
 * 
 * The returned object provides the following methods and fields:
 * 
 * - **`set(locale)`** Set a locale.
 * - **`language`** – {string} The language part of a locale. ('de' for 'de_DE' as well as for 'de')
 * - **`territory`** – {string} The territory part of a locale. ('DE' for 'de_DE', empty string for 'de')
 */
angular.module('dotHIVApp.services').factory('locale', function(dothivLocaleResource, $translate, $rootScope) {
    var locale = {
        _locale: null,
        _set: function(locale) {
                locale = locale || '';
                this._locale.locale = locale;
                var parts = locale.match(/^([a-zA-Z]+)(\_([a-zA-Z]+))?$/) || Array(4);
                this.language = parts[1] || '';
                this.territory = parts[3] || '';
                $translate.uses(this.language ? this.language : 'en');
        },
        language: '',
        territory: '',
        set: function(locale) {
                this._set(locale);
                this._locale.$put();
            }
    };

    // initialize locale service
    locale._locale = dothivLocaleResource.get(function() {
        locale._set(locale._locale.locale);
        $rootScope.$broadcast('localeInitialized');
    });

    return locale;
});
