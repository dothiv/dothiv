/**
 * Used to enter values into dhInput-elements
 */
angular.scenario.dsl('dhInput', function() {
    var chain = {};
    var supportInputEvent = true; // 'oninput' in document.createElement('div') && msie != 9;

    chain.enter = function(value, event) {
      return this.addFutureAction("dhInput '" + this.name + "' enter '" + value + "'", function($window, $document, done) {
        var input = $document.elements(this.name).filter(':input');
        input.val(value);
        input.trigger(event || (supportInputEvent ? 'input' : 'change'));
        done();
      });
    };

    return function(name) {
        this.name = name;
        return chain;
      };
});
