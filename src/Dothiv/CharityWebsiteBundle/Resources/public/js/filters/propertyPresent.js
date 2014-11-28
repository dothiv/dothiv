'use strict';

/**
 * Filters items according to their properties.
 *
 * config is a hash containing the object properties to check as keys and either null, true or false as value.
 *
 * null: do not check property (no need to supply it anyway)
 * true: property must exist
 * false: property must not exist
 */
angular.module('dotHIVApp.filters').filter('propertyPresent', function () {
    return function (input, config) {
        config = typeof config !== 'undefined' ? config : {};
        return input.filter(function (item) {
            for (var k in config) {
                if (config[k] !== null) {
                    if (config[k] === true) {
                        if (item[k] === undefined) {
                            return false;
                        }
                    } else {
                        if (item[k] !== undefined) {
                            return false;
                        }
                    }
                }
            }
            return true;
        });
    }
});
