var AdtoniqMessengerPlugin = (function () {
  'use strict';

  var module = {
    $button: null,
    $usersAll: null,
    $usersAds: null,
    $usersAdsPlus: null,
    $enabledBtn: null,
    $message: null,
    $confirmMessage: null,
    $rejectMessage: null,
    $confirmBtnText: null,
    $rejectBtnText: null,
    $customBtnClass: null,
    $showSelector: null,
    $selector: null,

    state: {
      adtoniqMsgEnabled: false,
      adtoniqMsgTargetedUsers: '',
      adtoniqMsgMessage: '',
      adtoniqMsgConfirm: '',
      adtoniqMsgReject: '',
      adtoniqMsgConfirmBtnText: '',
      adtoniqMsgRejectBtnText: '',
      adtoniqMsgCustomBtnText: '',
      selectedMceEditor: 'greeting',
      isDirty: false
    },

    isNotEmpty: function (str) {
      if (typeof str !== 'string') return false;
      if (!str.replace(/\s/g, '').length) return false; // check for only spaces
      return str.length > 0;
    },

    isValidForm: function () {
      return (
        true
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

      // check for enabled
      var value = e.target.value;
      if (e.target.id === 'adtoniqMsgEnabled') {
        value = e.target.checked;
      }

      // set the state
      var newObj = {};
      // check for radios
      if (
        e.target.id === 'adtoniq-msg-users-all' ||
        e.target.id === 'adtoniq-msg-users-adsonly' ||
        e.target.id === 'adtoniq-msg-users-adsplus'
      ) {
        newObj.adtoniqMsgTargetedUsers = value;
      } else {
        newObj[e.target.id] = value;
      }
      this.setState(newObj);

      // set submit button state
      this.setButtonState();
    },

    showMessageField: function (e) {
      var value = e.target.value;
      switch (value)
      {
        case 'greeting':
          document.getElementById('amsgGreeting').classList.remove('hidden');
          document.getElementById('amsgConfirm').classList.add('hidden');
          document.getElementById('amsgReject').classList.add('hidden');
          break;
        case 'confirm':
          document.getElementById('amsgGreeting').classList.add('hidden');
          document.getElementById('amsgConfirm').classList.remove('hidden');
          document.getElementById('amsgReject').classList.add('hidden');
          break;
        case 'reject':
          document.getElementById('amsgGreeting').classList.add('hidden');
          document.getElementById('amsgConfirm').classList.add('hidden');
          document.getElementById('amsgReject').classList.remove('hidden');
          break;
      }
      this.setState({ selectedMceEditor: value });
    },

    addListeners: function () {
      this.$enabledBtn.addEventListener('change', this.onChange.bind(this), false);
      this.$usersAll.addEventListener('change', this.onChange.bind(this), false);
      this.$usersAds.addEventListener('change', this.onChange.bind(this), false);
      this.$usersAdsPlus.addEventListener('change', this.onChange.bind(this), false);
      this.$message.addEventListener('input', this.onChange.bind(this), false);
      this.$confirmMessage.addEventListener('input', this.onChange.bind(this), false);
      this.$rejectMessage.addEventListener('input', this.onChange.bind(this), false);
      this.$confirmBtnText.addEventListener('input', this.onChange.bind(this), false);
      this.$rejectBtnText.addEventListener('input', this.onChange.bind(this), false);
      this.$customBtnClass.addEventListener('input', this.onChange.bind(this), false);
      this.$selector.addEventListener('input', this.onChange.bind(this), false);
    },

    render: function () {
      this.addListeners();

      // set the selector
      var selectedValue = this.$selector.value;
      this.showMessageField({ target: { value: selectedValue } });
      this.$selector.addEventListener('change', this.showMessageField.bind(this), false);

      // POSSIBLE to do:
      // set the listeners for visual mode
      // store state for selector and if in visual mode for that thing
      // if so -- get you state change from that instead of text area

      this.setState({
        adtoniqMsgEnabled: this.$enabledBtn.checked || false,
        adtoniqMsgTargetedUsers: document.querySelector('input[name="adtoniq-msg-users"]:checked').value || '',
        adtoniqMsgMessage: this.$message.value || '',
        adtoniqMsgConfirm: this.$confirmMessage.value || '',
        adtoniqMsgReject: this.$rejectMessage.value || '',
        adtoniqMsgConfirmBtnText: this.$confirmBtnText.value || '',
        adtoniqMsgRejectBtnText: this.$rejectBtnText.value || '',
        adtoniqMsgCustomBtnText: this.$customBtnClass.value || '',
        selectedMceEditor: selectedValue
      });

      // set immediate validation state for buttons
      this.setValidationState(this.$confirmBtnText, '');
      // this.setValidationState(this.$rejectBtnText, '');

      this.setButtonState();
    },

    init: function () {
      this.$button = document.querySelector('#AdtoniqMessengerForm #submit');
      this.$usersAll = document.getElementById('adtoniq-msg-users-all');
      this.$usersAds = document.getElementById('adtoniq-msg-users-adsonly');
      this.$usersAdsPlus = document.getElementById('adtoniq-msg-users-adsplus');
      this.$enabledBtn = document.getElementById('adtoniqMsgEnabled');
      this.$message = document.getElementById('adtoniqMsgMessage');
      this.$confirmMessage = document.getElementById('adtoniqMsgConfirm');
      this.$rejectMessage = document.getElementById('adtoniqMsgReject');
      this.$confirmBtnText = document.getElementById('adtoniqMsgConfirmBtnText');
      this.$rejectBtnText = document.getElementById('adtoniqMsgRejectBtnText');
      this.$customBtnClass = document.getElementById('adtoniqMsgCustomBtnText');
      this.$selector = document.getElementById('adtoniqMsgSelector');
      this.$mceTextField = document.getElementById('amsgGreeting');

      this.render();
    }
  };

  return module;

}());

if (typeof module !== 'undefined') {
  module.exports = AdtoniqMessengerPlugin;
}
