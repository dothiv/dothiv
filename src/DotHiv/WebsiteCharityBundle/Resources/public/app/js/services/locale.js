'use strict';

/**
 * @name dotHIVApp.services.locale
 * @requires dothivLocaleResource
 * 
 * @description
 * Manages the user's locale. This includes telling the server which locale to use.
 * 
 * The returned object provides the following methods and fields:
 * 
 * - **`set()`** Set a locale.
 *   - `locale` – {string} The locale to use from now on.
 */
angular.module('dotHIVApp.services').factory('locale', function(dothivLocaleResource) {
    var locale = {
        locale: null,
        set: function(locale) {
                this.locale.locale = locale;
                this.locale.$put();
            }
        };
    locale.locale = dothivLocaleResource.get();
    return locale;
});
