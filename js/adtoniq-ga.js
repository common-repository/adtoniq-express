var AdtoniqGAPlugin = (function () {
  'use strict';

  var module = {
    $accountNumber: null,
    $trafficSplit: null,
    $button: null,

    state: {
      gaProperty: '',
      gaTraffic: '',
      isDirty: false,
    },

    isNotEmpty: function (str) {
      if (typeof str !== 'string') return false;
      if (!str.replace(/\s/g, '').length) return false; // check for only spaces
      return str.length > 0;
    },

    isValidAcctNumber: function (str) {
      if (typeof str !== 'string') return false;
      return str.trim().length == 0 || (/^ua-\d{4,9}-\d{1,4}$/i).test(str);
    },

    isValidForm: function () {
      return (
        this.isValidAcctNumber(this.state.gaProperty)
      );
    },

    isValidTrafficSpilt: function (number) {
      return Number.parseInt(number, 10) <= 100;
    },

    setState: function (newState) {
      var changedState = Object.assign({}, this.state, newState);
      this.state = changedState;
    },

    setButtonState: function () {
      var isValid = this.isValidForm();
      var isDirty = this.state.isDirty;
      isValid && isDirty ?
        this.$button.classList.remove('btn-disabled') : this.$button.classList.add('btn-disabled'); // jshint ignore:line
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

      // check ga id validation
      if (e.target.id === 'gaProperty') {
        this.setValidationState(
          this.$accountNumber,
          this.isValidAcctNumber(this.state.gaProperty) ? '' : 'Invalid account number'
        );
      }

      // check target percent validation
      if (e.target.id === 'gaTraffic') {
        this.setValidationState(
          this.$trafficSplit,
          this.isValidTrafficSpilt(Number.parseInt(this.state.gaTraffic, 10)) ? '' : 'Must be a number between 0-100 percent'
        );
      }

      // set submit button state
      this.setButtonState();
    },

    addListeners: function () {
      this.$accountNumber.addEventListener('input', this.onChange.bind(this), false);
      this.$trafficSplit.addEventListener('input', this.onChange.bind(this), false);
    },

    render: function () {
      this.addListeners();
      this.setState({
        gaProperty: this.$accountNumber.value || '',
        gaTraffic: this.$trafficSplit.value || ''
      });
      if (this.isNotEmpty(this.state.gaProperty)) {
        this.setValidationState(
          this.$accountNumber,
          this.isValidAcctNumber(this.state.gaProperty) ? '' : 'Invalid account number'
        );
      }
      if (this.isNotEmpty(this.state.gaTraffic)) {
        this.setValidationState(
          this.$trafficSplit,
          this.isValidTrafficSpilt(Number.parseInt(this.state.gaTraffic, 10)) ? '' : 'Must be a number between 0-100 percent'
        );
      }
      this.setButtonState();
    },

    init: function () {
      // assign elements
      this.$accountNumber = document.getElementById('gaProperty');
      this.$trafficSplit = document.getElementById('gaTraffic');
      this.$button = document.querySelector('#AdtoniqGAForm #submit');

      // kick off the module
      this.render();
    }
  };

  return module;

}());

if (typeof module !== 'undefined') {
  module.exports = AdtoniqGAPlugin;
}
