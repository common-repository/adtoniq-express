<?php

/**
 * Notification alert class
 *
 * @since  0.1.0
 */

class AdtoniqProtection {

 /**
   *  Options array
   */
  public $options = array();

  /**
   * Build the class
   */
  function __construct() {
    add_action('init', array(&$this, 'get_options'));
    add_action('admin_init', array(&$this, 'adtoniq_protection_settings'));
  }

  /**
   * Get all option settings
   *
   * @since 4.0.0
   */
  function get_options() {
    $settings = array();
    $settings['status'] = get_option('adtoniq-protection-status');
    $settings['url'] = get_option('adtoniq-protection-url');
    $settings['cssSelector'] = get_option('adtoniq-protection-css');
    $settings['saved'] = get_option('adtoniq-protection-saved');

    $this->options = $settings;
  }

  function adtoniq_protection_settings() {
    register_setting( 'adtoniq-protection-settings-group', 'adtoniq-protection-status' );
    register_setting( 'adtoniq-protection-settings-group', 'adtoniq-protection-url' );
    register_setting( 'adtoniq-protection-settings-group', 'adtoniq-protection-css' );
    register_setting( 'adtoniq-protection-settings-group', 'adtoniq-protection-saved' );
  }

  function adtoniq_protection_head_injection() {
    $status = $this->options['status'];
    if ($status == 'allButHome' || $status == 'all')
      echo "<style>body{display:none;}</style>";
  }

  /**
   * Show save modal
   *
   * @since 4.0.0
   */
  function adtoniq_protection_render_update() {
    ?>
      <div id="successOnSave" class="notice notice-success is-dismissible">
        <p>Changes Saved!</p>
      </div>
    <?php
  }

  /**
   * Render main page layout.
   *
   * @since 4.0.0
   */
  function adtoniq_protection_render() {
    $protectStatus = $this->options['status'];
    $protectUrl = $this->options['url'];
    $protectCss = $this->options['cssSelector'];
    $saved = $this->options['saved'];
    // if ($saved === 'saved') {
    //   $this->adtoniq_protection_render_update();
    //   adtoniq_update_option('adtoniq-protection-saved', '', true);
    // }
  ?>
    <div class="well" data-help="protect">
      <h1 class="page-header">
        Protection
        <small class="muted right">Protect your content and functionality</small>
      </h1>
      <div class="well-container">
        <form method="post" id="AdtoniqProtectionForm" action="options.php" class="adtoniq-form form-horizontal">
          <?php settings_fields( 'adtoniq-protection-settings-group' ); ?>
          <?php do_settings_sections( 'adtoniq-protection-settings-group' ); ?>
          <input type="hidden" name="adtoniq-protection-saved" value="saved" />
          <div>
            <div class="form-group">
              <label for="adtoniqProtectionRadioGroup" class="col-sm control-label">
                Site Protection Status
              </label>
              <div class="col-">
                <div class="radio">
                  <label>
                    <input type="radio" id="adtoniq-protection-none" name="adtoniq-protection-status" value="none"
                    <?php echo ($protectStatus === 'none' || $protectStatus === false || $protectStatus === '') ? 'checked' : ''; ?>
                    />
                    No protection
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" id="adtoniq-protection-allButHome" name="adtoniq-protection-status" value="allButHome"
                    <?php echo ($protectStatus === 'allButHome') ? 'checked' : ''; ?>
                    />
                    Home page allowed but rest of site protected
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" id="adtoniq-protection-all" name="adtoniq-protection-status" value="all"
                    <?php echo ($protectStatus === 'all') ? 'checked' : ''; ?>
                    />
                    Entire site is protected
                  </label>
                </div>
                <div class="radio" style="display:none;"> <!-- Coming soon -->
                  <label>
                    <input type="radio" id="adtoniq-protection-css" name="adtoniq-protection-status" value="css"
                    <?php echo ($protectStatus === 'css') ? 'checked' : ''; ?>
                    />
                    Protect elements matching CSS Selector
                  </label>
                </div>
                <div id="protect-radio-description"></div>
              </div>
              <div class="col-sm" style="width: 31%;">
                Protection works in conjunction with messaging to lock users out of your site or parts of it as an incentive to opt in to choices that benefit you. If you enable protection, ad blocked users who have not opted in to your choice will be locked out. You can choose which parts of your site are protected.
              </div>
            </div>
            <div class="form-group" id="protection-url" style="display:none;">
              <label id="protection-label" for="adtoniq-protection-url" class="col-sm control-label">
                Send adblocked users to this page
              </label>
              <div class="col-sm">
                <input
                  type="text"
                  class="form-control"
                  id="adtoniqProtectionUrl"
                  name="adtoniq-protection-url"
                  placeholder="Enter URL"
                  value="<?php echo esc_attr( $protectUrl ); ?>"
                  tabindex="11"
                  aria-describedby="protectionUrlHelpBlock" />
                <span class="form-control-feedback" aria-hidden="true"></span>
              </div>
              <span id="protectionUrlHelpBlock" class="help-block col-xs-offset">Message Here</span>
            </div>
            <div class="form-group" id="protection-css" style="display:none;">
              <label id="protection-label" for="adtoniq-protection-css" class="col-sm control-label">
                Protect elements matching <a href="https://www.w3schools.com/cssref/css_selectors.asp">CSS Selector</a>
              </label>
              <div class="col-sm">
                <input
                  type="text"
                  class="form-control"
                  id="adtoniq-protection-css"
                  name="adtoniq-protection-css"
                  placeholder="Enter CSS Selector"
                  value="<?php echo esc_attr( $protectCss ); ?>"
                  tabindex="11" />
                <span class="form-control-feedback" aria-hidden="true"></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <input type="submit" name="submit" id="submit-protection" class="btn btn-primary btn-block" value="Save Changes">
          </div>
        </form>
      </div>
      <script type="text/javascript">
        AdtoniqProtectionPlugin.init();
      </script>
    </div>
   <?php
  }
}

$adtoniq_protection = new AdtoniqProtection();
