var AdtoniqAPIPlugin = (function () {
  'use strict';

  var module = {
    $apiKey: null,
    $apiSubmitButton: null,


    state: {
      adtoniqApiKey: '',
      isDirty: false,
    },

    isNotEmpty: function (str) {
      if (typeof str !== 'string') return false;
      if (!str.replace(/\s/g, '').length) return false; // check for only spaces
    },

    isValidAPIKey: function (str) {
      if (typeof str !== 'string') return false;
      if (str === '') return true;
      return (/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/i).test(str);
    },

    isValidForm: function () {
      return (
        this.isValidAPIKey(this.state.adtoniqApiKey)
      );
    },

    setState: function (newState) {
      var changedState = Object.assign({}, this.state, newState);
      this.state = changedState;
    },

    setButtonState: function () {
      var isValid = this.isValidForm();
      var isDirty = this.state.isDirty;
      isValid && isDirty ?
        this.$apiSubmitButton.classList.remove('btn-disabled') : this.$apiSubmitButton.classList.add('btn-disabled'); // jshint ignore:line
    },

    setValidationState: function (el, failureMessage) {
      var helpBlockId = el.getAttribute('aria-describedby');
      var helpBlock = document.getElementById(helpBlockId);
      var inputContainer = el.parentElement.parentElement;
      var icon = el.nextElementSibling;

      if (failureMessage) {
        inputContainer.className = 'form-group has-error has-feedback';
        icon.className = 'form-control-feedback icon-error';
        helpBlock.innerHTML = failureMessage;
        helpBlock.style.display = 'block';
      } else {
        inputContainer.className = 'form-group has-success has-feedback';
        icon.className = 'form-control-feedback icon-ok';
        helpBlock.style.display = 'none';
      }
    },

    onChange: function (e) {
      // check for dirty
      if (!this.state.isDirty) {
        this.setState({ isDirty: true });
      }

      // set the state
      var value = e.target.value;
      var newObj = {};
      newObj[e.target.id] = value;
      this.setState(newObj);

      // check api key validation
      if (e.target.id === 'adtoniqApiKey') {
        this.setValidationState(
          this.$apiKey,
          this.isValidAPIKey(this.state.adtoniqApiKey) ? '' : 'Invalid API key'
        );
      }

      // set submit button state
      this.setButtonState();
    },

    addListeners: function () {
      this.$apiKey.addEventListener('input', this.onChange.bind(this), false);
    },

    render: function () {
      this.addListeners();
      this.setState({
        adtoniqApiKey: this.$apiKey.value || ''
      });
      if (this.isNotEmpty(this.state.adtoniqApiKey)) {
        this.setValidationState(
          this.$apiKey,
          this.isValidAPIKey(this.state.adtoniqApiKey) ? '' : 'Invalid API key'
        );
      }
      this.setButtonState();
    },

    init: function () {
      // assign elements
      this.$apiKey = document.getElementById('adtoniqApiKey');
      this.$apiSubmitButton = document.querySelector('#AdtoniqAPIForm #submit-apiKey');

      // kick off the module
      this.render();
    }
  };

  return module;

}());

if (typeof module !== 'undefined') {
  module.exports = AdtoniqAPIPlugin;
}
