<?php

/**
 * Notification alert class
 *
 * @since  0.1.0
 */

class AdtoniqMessengerAlert {

  function __construct () {
    $is_enabled = get_option('adtoniq-msg-is-enabled');
    if (!empty($is_enabled)) {
      add_action( 'wp_enqueue_scripts', array(&$this,'render_alert') );
    }
  }

  // Anything in here could potentially get blocked. This is a placeholder for scripts
  // to include that are okay to be blocked.
  function render_alert () {
  }

}

$adtoniq_msg_alert = new AdtoniqMessengerAlert();
