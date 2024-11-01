<?php
/**
 * Adtoniq Adsense main class
 *
 * @package WordPress
 * @subpackage adtoniq_adsense
 * @since 4.0.0
 */


class AdtoniqAPI
{

  private $_apiKey;

  /**
   * Build the class
   */
  function __construct() {
    // add_action('init', array(&$this, 'get_options'));
    // add_action('admin_init', array(&$this, 'adtoniq_api_settings'));
  }

  /**
   * Get all option settings
   *
   * @since 4.0.0
   */
  function get_options() {
    // any options to add -- put them here
  }

  /**
   * Show save modal
   *
   * @since 4.0.0
   */
  function adtoniq_api_render_update() {
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
  function adtoniq_api_render() {
    if ($this->saved === 'saved') {
      $this->adtoniq_api_render_update();
      adtoniq_update_option('adtoniq-api-saved', '', true);
    }
  ?>
    <div class="well" data-help="apikey">
      <h1 class="page-header">
        Adtoniq Cloud Key
        <small class="muted right">
          <?php if (strlen($this->_apiKey) === 0) { ?>
          <a id="getAdtoniqCloudKey" href="<?php echo esc_attr(ADTONIQ_ACCT_SERVER); ?>?source=getkey">Get an Adtoniq Cloud key</a>
          <?php } else { ?>
          Thank you for using Adtoniq Cloud
          <?php } ?>
        </small>
      </h1>
      <div class="well-container">
        <form method="post" id="AdtoniqAPIForm" action="options.php" class="adtoniq-form form-horizontal">
          <?php settings_fields( 'adtoniq-api-settings-group' ); ?>
          <?php do_settings_sections( 'adtoniq-api-settings-group' ); ?>
          <input type="hidden" name="adtoniq-api-saved" value="saved" />
          <div class="form-group">
            <label for="adtoniq-api-key" class="col-sm control-label">
              Enter your Adtoniq Cloud Key
            </label>
            <div class="col-sm">
              <input
                type="text"
                class="form-control"
                id="adtoniqApiKey"
                name="adtoniq-api-key"
                placeholder="Enter API key"
                value="<?php echo esc_attr( $this->_apiKey ); ?>"
                tabindex="1"
                aria-describedby="apiHelpBlock"/>
                <span class="form-control-feedback" aria-hidden="true"></span>
            </div>
            <span id="apiHelpBlock" class="help-block col-xs-offset">Message Here</span>
          </div>
          <div class="form-group">
            <input type="submit" name="submit" id="submit-apiKey" class="btn btn-primary btn-block" value="Save Changes">
          </div>
        </form>
        <p class="lead">
          <?php if (strlen($this->_apiKey) === 0) { ?>
            Contact Adtoniq at support@adtoniq.com to get an API Key, and paste it here.
          <?php } else { ?>
            Thank you for using Adtoniq.
          <?php } ?>
        </p>
      </div>
      <script type="text/javascript">
        AdtoniqAPIPlugin.init();
      </script>
    </div>
  <?php
  }

  /**
   * Init function.
   *
   * @since 4.0.0
   */
  function adtoniq_api_init($apiKey) {
    $this->_apiKey = $apiKey;
    $this->saved = get_option('adtoniq-api-saved');
    $this->adtoniq_api_render();
  }

  /**
   * Register settings on init.
   *
   * @since 4.0.0
   */

  function adtoniq_api_settings() {
    register_setting( 'adtoniq-api-settings-group', 'adtoniq-api-key' );
    register_setting( 'adtoniq-api-settings-group', 'adtoniq-head-injection' );
    register_setting( 'adtoniq-api-settings-group', 'adtoniq-lastUpdate' );
    register_setting( 'adtoniq-api-settings-group', 'adtoniq-lastVersion' );
    register_setting( 'adtoniq-api-settings-group', 'adtoniq-is-private' );
    register_setting( 'adtoniq-api-settings-group', 'adtoniq-api-saved' );
    register_setting( 'adtoniq-api-settings-group', 'adtoniq-master-control' );
    register_setting( 'adtoniq-api-settings-group', 'adtoniq-waisa-pageid' );
    register_setting( 'adtoniq-api-settings-group', 'adtoniq-integration-url' );
  }
}

$adtoniq_api = new AdtoniqAPI();
