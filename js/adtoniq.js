var Adtoniq = (function () {
  'use strict';

  var module = {
    state: {
      version: '4.0.9.6'
    },

    isNotEmpty: function (str) {
      if (typeof str !== 'string') return false;
      if (!str.replace(/\s/g, '').length) return false; // check for only spaces
      return str.length > 0;
    },

    onFocus: function () {
     var iframe = document.getElementById('anal-iframe');
     if (iframe)
      iframe.contentWindow.postMessage({focus: 'focus'}, '*');
    },

    onBlur: function () {
     var iframe = document.getElementById('anal-iframe');
     if (iframe)
      iframe.contentWindow.postMessage({focus: 'blur'}, '*');
    },

    setCookie: function (name, value, days) {
      var expires = '';
      if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = '; expires=' + date.toUTCString();
      }
      document.cookie = name + '=' + value + expires + '; path=/';
    },

    getCookie: function (name) {
      var value = '; ' + document.cookie;
      var parts = value.split('; ' + name + '=');
      if (parts.length === 2) {
        return parts.pop().split(';').shift();
      }
    },

    onUpdateDefintions: function() {
      jQuery(document).ready(function($) {
        var data = {
          'action': 'adtoniq_update',
          'adtoniqAction': 'requestJSUpdate'
        };
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {
          $("#adtoniq_ajax_response").text(response);
        });
      });
    },

    setVisiblePanels: function (tabType) {
      if (!tabType || typeof tabType !== 'string') { return false; }
      var cookieName = tabType + 'tab';
      if (typeof this.getCookie(cookieName) !== 'undefined') {
        var cookie = this.getCookie(cookieName);
        var tabs = document.querySelectorAll('#' + tabType + 'FeaturesNav li');
        var panels = document.querySelectorAll('#' + tabType + 'Features .tab-pane');
        for (var i = 0; i < tabs.length; i++) {
          tabs[i].className = '';
        }
        for (var j = 0; j < panels.length; j++) {
          panels[j].className = 'tab-pane fade';
        }
        var selectedTab = cookie + '-tab';
        var selectedPanel = cookie + '-panel';
        var selectedTabElement = document.getElementById(selectedTab);
        var selectedPanelElement = document.getElementById(selectedPanel);
        if (selectedTabElement) {
          selectedTabElement.parentElement.className = 'active';
        }
        if (selectedPanelElement) {
          selectedPanelElement.className = 'tab-pane fade in active';
        }
      } else {
        console.log('no ' + tabType + ' cookie');
      }
    },

    setFreeTab: function (tabName) {
      this.setCookie('freetab', tabName, 1);
    },

    getFreeTab: function() {
      return this.getCookie('freetab');
    },

    setPremiumTab: function (tabName) {
      this.setCookie('premiumtab', tabName, 1);
    },

    onSave: function ($alert) {
      if (!$alert) { return false; }
      var delay = setTimeout(function () {
        $alert.classList.add('fade-out');
        $alert.removeEventListener('transitionend', this.removeAlert, false);
        $alert.removeEventListener('webkitTransitionEnd', this.removeAlert, false);
      }, 5000);
      $alert.addEventListener('transitionend', this.removeAlert, false);

      // if click occurs before fade
      $alert.addEventListener('click', function () {
        clearTimeout(delay);
        $alert.removeEventListener('transitionend', this.removeAlert, false);
        $alert.removeEventListener('webkitTransitionEnd', this.removeAlert, false);
      }, false);
    },

    removeAlert: function () {
      var $alert = document.getElementById('successOnSave');
      var $wrap = document.querySelector('#messenger-panel .well');
      $wrap.removeChild($alert);
    },

    init: function () {
      var interval = setInterval(function() {
        var ifr = document.getElementById('adtoniqMsgMessage_ifr');
        if (ifr) {
          ifr.contentDocument.body.style.minWidth = '100%';
          clearInterval(interval);
        }
      }, 250);

      window.addEventListener('blur', this.onBlur);
      window.addEventListener('focus', this.onFocus);

      var freetab = Adtoniq.getFreeTab();
      if (!freetab) {
        freetab = 'documentation';
        Adtoniq.setFreeTab(freetab);
      }
      Adtoniq.setPremiumTab('apikey');

      this.setVisiblePanels('free');
      this.setVisiblePanels('premium');

      var $alert = document.getElementById('successOnSave');
      if ($alert) { this.onSave($alert); }
   }
  };

  return module;

}());

if (typeof module !== 'undefined') {
  module.exports = Adtoniq;
}
