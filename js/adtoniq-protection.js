var AdtoniqProtectionPlugin = (function () {
  'use strict';

  var module = {
    $option1: null,
    $option2: null,
    $protectionURL: null,
    $protectionSubmitButton: null,

    state: {
      adtoniqProtectionTargetedUsers: 'none',
      adtoniqProtectionUrl: '',
      isDirty: false,
    },

    isNotEmpty: function (str) {
      if (typeof str !== 'string') return false;
      if (!str.replace(/\s/g, '').length) return false; // check for only spaces
      return str.length > 0;
    },

    isValidUrl: function (str) {
      if (typeof str !== 'string' ) { return false; }
      return (/^((https?):\/\/)?([w|W]{3}\.)+[a-zA-Z0-9\-\.]{3,}\.[a-zA-Z]{2,}(\.[a-zA-Z]{2,})?$/).test(str);
    },

    isValidForm: function () {
      if (this.state.adtoniqProtectionTargetedUsers === 'none') {
        return true;
      }
      return this.isNotEmpty(this.state.adtoniqProtectionUrl) && this.isValidUrl(this.state.adtoniqProtectionUrl);
    },

    setState: function (newState) {
      var changedState = Object.assign({}, this.state, newState);
      this.state = changedState;
    },

    setButtonState: function () {
      var isValid = this.isValidForm();
      var isDirty = this.state.isDirty;
      isValid && isDirty ?
        this.$protectionSubmitButton.classList.remove('btn-disabled') : this.$protectionSubmitButton.classList.add('btn-disabled'); // jshint ignore:line
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
      var newObj = {};

      if (
        e.target.id === 'adtoniq-protection-none' ||
        e.target.id === 'adtoniq-protection-allButHome' ||
        e.target.id === 'adtoniq-protection-all'
      ) {
        newObj.adtoniqProtectionTargetedUsers = e.target.value;
      } else {
        newObj[e.target.id] = e.target.value;
      }

      this.setState(newObj);

      if (e.target.id === 'adtoniqProtectionUrl') {
        this.setValidationState(
          document.getElementById(e.target.id),
          this.isValidUrl(e.target.value) && this.isNotEmpty(e.target.value) ? '' : 'You must enter a valid URL'
        );
      }

      this.setButtonState();
      this.setURLVisibility();
    },

    setURLVisibility: function () {
      // control display of URL field
      this.$protectionURLContainer.style.display =
        (this.state.adtoniqProtectionTargetedUsers === 'allButHome' ||
          this.state.adtoniqProtectionTargetedUsers === 'all') ?
          'block' : 'none';
    },

    addListeners: function () {
      this.$option1.addEventListener('change', this.onChange.bind(this), false);
      this.$option2.addEventListener('change', this.onChange.bind(this), false);
      this.$option3.addEventListener('change', this.onChange.bind(this), false);
      this.$protectionURL.addEventListener('input', this.onChange.bind(this), false);
    },

    render: function () {
      this.addListeners();
      this.setState({
        adtoniqProtectionTargetedUsers: document.querySelectorAll('input[name="adtoniq-protection-status"]:checked')[0].value || '',
        adtoniqProtectionUrl: this.$protectionURL.value || ''
      });
      if (this.isNotEmpty(this.$protectionURL.value)) {
        this.setValidationState(
          this.$protectionURL,
          this.isValidUrl(this.$protectionURL.value) && this.isNotEmpty(this.$protectionURL.value) ? '' : 'You must enter a valid URL'
        );
      }
      this.setButtonState();
      this.setURLVisibility();
    },

    init: function () {
      // assign elements
      this.$option1 = document.getElementById('adtoniq-protection-none');
      this.$option2 = document.getElementById('adtoniq-protection-allButHome');
      this.$option3 = document.getElementById('adtoniq-protection-all');
      this.$protectionURLContainer = document.getElementById('protection-url');
      this.$protectionURL = document.getElementById('adtoniqProtectionUrl');
      this.$protectionSubmitButton = document.querySelector('#AdtoniqProtectionForm #submit-protection');

      // kick off the module
      this.render();
    }
  };

  return module;

}());

if (typeof module !== 'undefined') {
  module.exports = AdtoniqProtectionPlugin;
}
