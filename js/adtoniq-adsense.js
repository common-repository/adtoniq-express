var AdtoniqAdsensePlugin = (function () {
  'use strict';

  var module = {
    $adSenseAccountNumber: null,
    $adSenseCssSelector: null,
    $adSenseCodeSnippet: null,
    $adSenseSubmitButton: null,
    $adSenseMaxAdUnits: null,


    state: {
      asPublisherId: '',
      asCssSelector: '',
      codeSnippet: '',
      asMaxAds: 0,
      adtoniqAsEnabled: null,
      isDirty: false,
    },

    isNotEmpty: function (str) {
      if (typeof str !== 'string') return false;
      if (!str.replace(/\s/g, '').length) return false; // check for only spaces
      return str.length > 0;
    },

    isValidAdSenseAcctNumber: function (str) {
      if (typeof str !== 'string') return false;
      return (/([a-z][a-z]-)?pub-\d{16}/i).test(str);
    },

    isValidCodeSnippet: function (str) {
      if (typeof str !== 'string') return false;
      return true;
    },

    isValidCssSelector: function (str) {
      if (typeof str !== 'string') return false;
      try {
        if (str)
          document.querySelectorAll(str);
        return true;
      } catch {
        return false;
      }
      
      return true;
    },

    isValidMaxAdUnits: function (number) {
      return Number.parseInt(number, 10) <= 100 && Number.parseInt(number, 10) >= 0;
    },

    isValidForm: function () {
      return (
        this.isNotEmpty(this.state.asPublisherId) &&
        this.isValidAdSenseAcctNumber(this.state.asPublisherId) &&
        this.isValidCssSelector(this.state.asCssSelector) &&
        this.isValidMaxAdUnits(Number.parseInt(this.state.asMaxAds, 10))
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
        this.$adSenseSubmitButton.classList.remove('btn-disabled') : this.$adSenseSubmitButton.classList.add('btn-disabled'); // jshint ignore:line
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

      // check for enabled
      var value = e.target.value;
      if (e.target.id === 'adtoniqAsEnabled') {
        value = e.target.checked;
      }

      // set the state
      var newObj = {};
      newObj[e.target.id] = value;
      this.setState(newObj);

      // check adsense id validation
      if (e.target.id === 'asPublisherId') {
        this.setValidationState(
          this.$adSenseAccountNumber,
          this.isValidAdSenseAcctNumber(this.state.asPublisherId) ? '' : 'Invalid account number'
        );
      }

      // check css selector validation
      if (e.target.id === 'asCssSelector') {
        this.setValidationState(
          this.$cssSelector,
          this.isValidCssSelector(this.state.asCssSelector) ? '' : 'Invalid CSS Selector'
        );
      }

      // check adsense num ad units
      if (e.target.id === 'asMaxAds') {
        this.setValidationState(
          this.$adSenseMaxAdUnits,
          this.isValidMaxAdUnits(Number.parseInt(this.state.asMaxAds,10)) ? '' : 'Enter a number from 0-100'
        );
      }

      // set submit button state
      this.setButtonState();
    },

    addListeners: function () {
      this.$adSenseAccountNumber.addEventListener('input', this.onChange.bind(this), false);
      this.$cssSelector.addEventListener('input', this.onChange.bind(this), false);
      this.$codeSnippet.addEventListener('input', this.onChange.bind(this), false);
      this.$adSenseMaxAdUnits.addEventListener('input', this.onChange.bind(this), false);
      this.$adSenseEnabled.addEventListener('change', this.onChange.bind(this), false);
    },

    render: function () {
      this.addListeners();
      this.setState({
        asPublisherId: this.$adSenseAccountNumber.value || '',
        asMaxAds: this.$adSenseMaxAdUnits.value || ''
      });

      if (this.isNotEmpty(this.state.asPublisherId)) {
        this.setValidationState(
          this.$adSenseAccountNumber,
          this.isValidAdSenseAcctNumber(this.state.asPublisherId) ? '' : 'Invalid account number'
        );
      }

      if (this.isNotEmpty(this.state.asCssSelector)) {
        this.setValidationState(
          this.$cssSelector,
          this.isValidCssSelector(this.state.asCssSelector) ? '' : 'Invalid CSS Selector'
        );
      }

      if (this.isNotEmpty(this.state.asMaxAds)) {
        this.setValidationState(
          this.$adSenseMaxAdUnits,
          this.isValidMaxAdUnits(Number.parseInt(this.state.asMaxAds,10)) ? '' : 'Enter a number from 0-100'
        );
      }
      this.setButtonState();
    },

    init: function () {
      // assign elements
      this.$adSenseAccountNumber = document.getElementById('asPublisherId');
      this.$cssSelector = document.getElementById('asCssSelector');
      this.$codeSnippet = document.getElementById('asCodeSnippet');
      this.$adSenseMaxAdUnits = document.getElementById('asMaxAds');
      this.$adSenseEnabled = document.getElementById('adtoniqAsEnabled');
      this.$adSenseSubmitButton = document.querySelector('#AdtoniqASForm #submit');

      // kick off the module
      this.render();
    }
  };

  return module;

}());

if (typeof module !== 'undefined') {
  module.exports = AdtoniqAdsensePlugin;
}
