(function () {
  'use strict';

  var adtoniqMsgAlertData = !!window.adtoniqAlertData ? window.adtoniqAlertData : null;
  var $alert = createAlertBar();
  var transDelay = null;
  var showMessage = true;
  var adtoniqCookie = getCookie('adtoniq_choice');

  var makeAnchor = function(href) {
    var a = document.createElement("a");
    a.href = href;
    return a;
  };

  function isRedirect() {
    var u = getProtectionUrl();
    return adtoniqMsgAlertData.protectionStatus != 'none' && u && makeAnchor(u).pathname == location.pathname;
  }

  window.addEventListener('DOMContentLoaded', function () {
    if (!window.adtoniq) {
      console.error('Cannot run messenger bar module -- adtoniq global is not found');
      return false;
    }
    if (!adtoniqMsgAlertData) {
      console.error('Cannot run messenger bar module -- no data found');
      return false;
    }
    if ( (adtoniqMsgAlertData.targetedUsers === 'all' && ! adtoniqCookie) || isRedirect()) {
      showAlertBar();
    } else {
      if (adtoniqMsgAlertData.targetedUsers === 'adsplus')
        adtoniq.onAnalyticsBlocked(analyticsAlertBar);
      if (adtoniqMsgAlertData.targetedUsers === 'adsonly')
        adtoniq.onBlocked(adsAlertBar);
    }
  }, false);

  function getProtectionUrl() {
    var url = adtoniqMsgAlertData.protectionUrl;
    if (url.length == 0)
      return "";
    if (url.substr(0, 1) != '/')
      url = '/' + url;
    if (url.substr(url.length -1, 1) != '/')
      url = url + '/';
    return url;
  }

  function protect(adtoniqCookie) {
    var ret = false, url = getProtectionUrl();
    if (url.length > 0) {
      switch (adtoniqMsgAlertData.protectionStatus) {
        case 'none':
          break;
        case 'allButHome':
          ret = location.pathname != "/" && location.pathname != url;
          if (ret && (!adtoniqCookie || adtoniqCookie != 'track'))
            location.href = url;
          else
            document.body.style.display = 'block !important';
          break;
        case 'all':
          ret = location.pathname != url;
          if (ret && (!adtoniqCookie || adtoniqCookie != 'track'))
            location.href = url;
          else
            document.body.style.display = 'block !important';
          break;
        case 'css':
          break;
      }
    }
    return ret;
  }

  function analyticsAlertBar (isAnalyticsBlocked, adtoniqCookie) {
    if (isAnalyticsBlocked && !adtoniqCookie) {
      showAlertBar();
    }
  }

  function adsAlertBar (adtoniqCookie) {
    if (!adtoniqCookie) {
      showAlertBar();
    }
  }

  function showAlertBar() {
    protect(adtoniqCookie);
    document.body.appendChild($alert);
    transDelay = setTimeout(function () {
      $alert.classList.add('reveal-alert');
      $alert.addEventListener('click', onAlertChoice, false);
    }, 50);
  }

  function onAlertChoice (e) {
    if (e.target.id === 'adtoniq-msg-track-btn' || e.target.id === 'adtoniq-msg-no-track-btn') {
      if (typeof adtoniq.optIn === 'function') {
        if (e.target.name == 'track')
          adtoniq.optIn();
        else
          adtoniq.optOut();
        showNextMessage(e.target.name);
        $alert.removeEventListener('click', onAlertChoice, false);
        clearTimeout(transDelay);
      }
    }
  }

  function createAlertBar () {
    var $alertBar = document.createElement('div');
    $alertBar.id = 'adtoniq-msg-bar';
    $alertBar.innerHTML = renderAlertBar();
    return $alertBar;
  }

  function renderAlertBar (state) {
    var confirmBtn = adtoniqMsgAlertData.confirmBtnText.length > 0 ? '<button id="adtoniq-msg-track-btn" name="track" class="btn-msg-bar ' + adtoniqMsgAlertData.customBtnClass + '">' + adtoniqMsgAlertData.confirmBtnText + '</button>' : '';
    var rejectBtn = adtoniqMsgAlertData.rejectBtnText.length > 0 ? '<button id="adtoniq-msg-no-track-btn" name="do-not-track" class="btn-msg-bar ' + adtoniqMsgAlertData.customBtnClass + '">' + adtoniqMsgAlertData.rejectBtnText + '</button>' : '';
    var closeBtn = '<button id="adtoniq-msg-close-btn" name="close" class="btn-msg-bar">Close</button>';
    var alertBarHTML =  !state ? adtoniqMsgAlertData.greetingMsg : state === 'confirm' ? adtoniqMsgAlertData.confirmMsg : adtoniqMsgAlertData.rejectMsg;
    alertBarHTML = '<div id="adtoniq-msg">' + alertBarHTML + '</div>';
    var buttonHTML = !state ? confirmBtn + rejectBtn : closeBtn;
    alertBarHTML += '<div id="adtoniq-btns">' + buttonHTML + '</div>';
    return alertBarHTML;
  }

  function showNextMessage (msg) {
    $alert.innerHTML = renderAlertBar(msg === 'track' ? 'confirm' : 'reject');
    $alert.addEventListener('click', hideBar, false);
  }

  function hideBar () {
    $alert.classList.remove('reveal-alert');
    $alert.addEventListener('click', hideBar, false);
    $alert.addEventListener('transitionend', removeBar, false);
  }

  function removeBar () {
    document.body.removeChild($alert);
    $alert.removeEventListener('transitionend', removeBar, false);
  }

  function getCookie (name) {
    var value = '; ' + document.cookie;
    var parts = value.split('; ' + name + '=');
    if (parts.length === 2) {
      return parts.pop().split(';').shift();
    }
    return null;
  }

}());
