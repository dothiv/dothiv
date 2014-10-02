'use strict';

describe('the form manager service', function() {

    var scope, formManager, translate;

    // load dotHIV services
    beforeEach(module('dotHIVApp.services'));

    // mock the $translate service
    beforeEach(module(function($provide) {
        $provide.factory('$translate', function() {
            var prefix = '';
            var $translate = function(val) {
                return prefix + val;
            };
            $translate.setPrefix = function(p) {
                prefix = p;
            };
            return $translate;
        });
    }));

    // mock the $scope service
    beforeEach(module(function($provide) {
        $provide.factory('$scope', function() {
            var s =
                {
                    $watches: {},
                    $watch: function(name, fn) {
                        this.$watches[name] = fn;
                    }
                };
            return s;
        });
    }));

    // load formManager service
    beforeEach(inject(function($injector) {
        scope = $injector.get('$scope');
        translate = $injector.get('$translate');
        formManager = $injector.get('formManager');
        expect(scope).toBeDefined();
        expect(translate).toBeDefined();
        expect(formManager).toBeDefined();
    }));

    var formFields = ['name', 'surname', 'email', 'password', 'passwordrepeat'];

    // init a mock form
    function initForm() {
        if (!angular.isDefined(scope.myForm))
            scope.myForm = {};
        angular.forEach(formFields, function(value) {
            scope.myForm[value] =
                {
                    $invalid: false,
                    $error: {},
                    $modelValue: undefined
                };
            scope.myForm[value].$name = value;
        });
        scope.$watches['myForm']();
        scope.$watches['myData']();
    }

    var myFormManager;

    beforeEach(function() {
        myFormManager = formManager('myForm', scope, 'myData');
        expect(myFormManager).toBeDefined();
        initForm();
    });

    describe('initalization', function() {

        it('should set a watch on the form object', function() {
            expect(scope.$watches['myForm']).toBeDefined();
        });

        it('should set a watch on the data object when the form is initialized', function() {
            initForm();
            expect(scope.$watches['myData']).toBeDefined();
        });

        it('should define the focus object, populate the tooltip object and set failed to false', function() {
            initForm();
            expect(scope.myForm.$focus).toEqual({});
            expect(scope.myForm.$failed).toBe(false);
            expect(scope.myForm.$tooltip).toBeDefined();
            expect(Object.keys(scope.myForm.$tooltip)).toEqual(formFields);
        });

        it('should not overwrite existing tooltip and focus objects, but should add the tooltips', function() {
            var data = {};
            var focus = {};
            var tooltip = {};
            scope.myForm = {$focus: focus, $tooltip: tooltip};
            scope.myData = data;
            initForm();
            expect(scope.myData).toBe(data);
            expect(scope.myForm.$focus).toBe(focus);
            expect(scope.myForm.$tooltip).toBe(tooltip);
            expect(Object.keys(scope.myForm.$tooltip)).toEqual(formFields);
        });

        it('should use tooltip keys that start with [formname].form.[formelementname].tooltip.[state]', function() {
            initForm();
            angular.forEach(formFields, function(value) {
                expect(scope.myForm.$tooltip[value]).toMatch(new RegExp('^myForm\.form\.' + value + '\.tooltip\.[a-zA-Z]+$'));
            });
        })

    });

    describe('tooltips', function() {

        it('should update when the form data changes', function() {
            scope.myForm.name.$invalid = false;
            scope.$watches['myData']();
            expect(scope.myForm.$tooltip.name).toEqual('myForm.form.name.tooltip.default');

            scope.myForm.name.$invalid = true;
            scope.myForm.name.$error['myError'] = {};
            scope.myForm.$failed = true;
            scope.$watches['myData']();
            expect(scope.myForm.$tooltip.name).toEqual('myForm.form.name.tooltip.invalid.myError');
        });

        it('should be specific if on error when no translation is defined', function() {
            scope.myForm.name.$invalid = true;
            scope.myForm.name.$error['myError'] = {};
            scope.myForm.$failed = true;
            scope.$watches['myData']();
            expect(scope.myForm.$tooltip.name).toEqual('myForm.form.name.tooltip.invalid.myError');
        });

        it('should not be specific if an unspecific translation is defined', function() {
            translate.setPrefix('!');
            scope.myForm.name.$invalid = true;
            scope.myForm.name.$error['myError'] = {};
            scope.myForm.$failed = true;
            scope.$watches['myData']();
            expect(scope.myForm.$tooltip.name).toEqual('!myForm.form.name.tooltip.invalid');
        });

        it('should not display an error if the form didn\'t fail yet', function() {
            scope.myForm.name.$invalid = true;
            scope.myForm.name.$error['myError'] = {};
            scope.myForm.$failed = false;
            scope.$watches['myData']();
            expect(scope.myForm.$tooltip.name).toEqual('myForm.form.name.tooltip.default');
        });

    });

    describe('showServerError() function', function() {

        it('should be defined', function() {
            expect(myFormManager.showServerError).toBeDefined();
        });

        it('should set focus to the first field', function() {
            scope.myForm.name.$invalid = true;
            myFormManager.showServerError('foo error');
            expect(scope.myForm.$focus.name).toBeDefined();
            var val = scope.myForm.$focus.name;
            scope.myForm.name.$invalid = false;
            myFormManager.showServerError('bar error');
            expect(scope.myForm.$focus.name).not.toEqual(val);
        });

        it('should set the tooltip text correctly', function() {
            myFormManager.showServerError('foo');
            expect(scope.myForm.$tooltip.name).toEqual('foo');
        });

        it('should reset the tooltip when data is changed', function() {
            scope.myForm.name.$invalid = false;
            scope.$watches['myData']();
            expect(scope.myForm.$tooltip.name).toEqual('myForm.form.name.tooltip.default');
        });

    });

    describe('showServerFormError() function', function() {

        var response = { data: { errors: { children: { password: { errors: [ "foo", "bar" ] } } } } };

        beforeEach(function() {
            scope.myForm.$failed = true;
            myFormManager.showServerFormError(response);
        });

        it('should be defined', function() {
            expect(myFormManager.showServerFormError).toBeDefined();
        });

        it('should set focus to the first invalid field', function() {
            expect(scope.myForm.$tooltip.password).toEqual('foo');
        });

        it('should reset the tooltip when data is changed and invalid', function() {
            scope.myForm.password.$invalid = true;
            scope.$watches['myData']();
            expect(scope.myForm.$tooltip.password).toEqual('myForm.form.password.tooltip.invalid');
        });

        it('should reset the tooltip when data is changed and valid', function() {
            scope.myForm.password.$invalid = false;
            scope.$watches['myData']();
            expect(scope.myForm.$tooltip.password).toEqual('myForm.form.password.tooltip.default');
        });

    });

    describe('fail() function', function() {

        beforeEach(function() {
            myFormManager.fail();
        })

        it('should be defined', function() {
            expect(myFormManager.fail).toBeDefined();
        });

        it('should set the form to failed', function() {
            expect(scope.myForm.$failed).toBe(true);
        });

        it('should set the focus to the first invalid field when not using subforms', function() {
            scope.myForm.password.$invalid = true;
            scope.myForm.$error = { password: [ scope.myForm.password ] };
            myFormManager.fail();
            expect(scope.myForm.$focus.password).toBeDefined();
            var val = scope.myForm.$focus.password;
            myFormManager.fail();
            expect(scope.myForm.$focus.password).not.toEqual(val);
        });

        it('should set the focus to the first invalid field when using subforms', function() {
            scope.myForm.password.$invalid = true;
            scope.myForm.$error = { password: [ scope.myForm ] };
            myFormManager.fail();
            expect(scope.myForm.$focus.password).toBeDefined();
            var val = scope.myForm.$focus.password;
            myFormManager.fail();
            expect(scope.myForm.$focus.password).not.toEqual(val);
        });

        it('should set the tooltip correctly', function() {
            scope.myForm.password.$invalid = true;
            scope.myForm.password.$error['length'] = {};
            myFormManager.fail();
            expect(scope.myForm.$tooltip.password).toEqual('myForm.form.password.tooltip.invalid.length');
        });

    });

});
