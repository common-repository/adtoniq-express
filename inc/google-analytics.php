<?php
/**
 * Adtoniq Google Analytics main class
 *
 * @package WordPress
 * @subpackage adtoniq_ga
 * @since 4.0.0
 */


class AdtoniqGA
{

  private $_apiKey;

  /**
   * Build the class
   */
  function __construct() {
    // add_action('init', array(&$this, 'get_options'));
    // add_action('admin_init', array(&$this, 'adtoniq_ga_settings'));
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
  function adtoniq_ga_render_update() {
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
  function adtoniq_ga_render() {
    $ga_id = get_option('adtoniq-ga-property-id');
    $ga_split = get_option('adtoniq-ga-traffic-split');
    $ga_saved = get_option('adtoniq-ga-saved');
  	if ($ga_saved === 'saved') {
  		$this->adtoniq_ga_render_update();
  		adtoniq_update_option('adtoniq-ga-saved', '', true);
  	}
  ?>
    <div class="well" data-help="a4ga">
      <h1 class="page-header">
        Google Analytics
        <small class="muted right">Use Google Analytics</small>
      </h1>
      <div class="well-container">
        <p class="lead">
        <?php if (strlen($this->_apiKey) === 0) { ?>
          This feature requires an Adtoniq Cloud account. <a id="signUpAcctGA" href="<?php echo esc_attr(ADTONIQ_ACCT_SERVER); ?>?source=getkey">Sign up for an Adtoniq
          Cloud account</a> and receive one month free. No credit card is required unless you
          want to continue using Adtoniq server features after your one month trial.
        <?php } else { ?>

          <form method="post" id="AdtoniqGAForm" class="adtoniq-ga-form form-horizontal" action="options.php">
            <?php settings_fields( 'adtoniq_ga_settings-group' ); ?>
            <?php do_settings_sections( 'adtoniq_ga_settings-group' ); ?>
            <input type="hidden" name="adtoniq-ga-saved" value="saved" />
            <div class="form-group">
              <label for="adtoniq-ga-property-id" class="col-sm control-label">
                Tracking ID
              </label>
              <div class="col-sm">
                <input
                  type="text"
                  class="form-control"
                  id="gaProperty"
                  name="adtoniq-ga-property-id"
                  placeholder="UA-XXXXXXX-X"
                  value="<?php echo esc_attr( $ga_id ); ?>"
                  tabindex="1"
                  aria-describedby="trackingHelpBlock">
                  <span class="form-control-feedback" aria-hidden="true"></span>
              </div>
              <div class="col-sm vert-center">
                <p>Enter your full Google Analytics tracking ID</p>
              </div>
              <span id="trackingHelpBlock" class="help-block col-xs-offset">Message Here</span>
            </div>
            <div class="form-group">
              <label for="adtoniq-ga-traffic-split" class="col-sm control-label">
                Traffic Split
              </label>
              <div class="col-sm">
                <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="100"
                  step="1"
                  id="gaTraffic"
                  name="adtoniq-ga-traffic-split"
                  value="<?php echo esc_attr( $ga_split ); ?>"
                  tabindex="1"
                  style="calc(100% - 40px)"
                  aria-describedby="trafficHelpBlock">
                  <span class="form-control-feedback" style="right:50px" aria-hidden="true"></span>
                  <span class="input-group-addon" style="position:absolute; top: 0; line-height:inherit; right: 10px; width: 40px; height: 34px;" >%</span>
              </div>
              <div class="col-sm vert-center">
                <p><br/>Traffic splitting allows you to stay within the Google Analytics data collection limit. Read more about data collection limits and quotas <a href="https://developers.google.com/analytics/devguides/collection/gajs/limits-quotas">here</a>.</p>
              </div>
              <span id="trafficHelpBlock" class="help-block col-xs-offset">Message Here</span>
            </div>
            <div class="form-group">
              <input type="submit" name="submit" id="submit" class="btn btn-primary btn-block" value="Save Changes" tabindex="1">
            </div>
          </form>
          <p>Search for Adtoniq in the Google Analytics solution gallery to download our
            ad blocking best practice widgets and segments. You'll need to follow the instructions
            in our video on how to set up Google Analytics
            <a id="dimensionsYouTubeVideo" href="https://www.youtube.com/watch?v=JU-T7XV7uTM">custom dimensions</a> to leverage
            Adtoniq's best practice widgets.</p>
          Thank you for using Adtoniq. You can <a id="signIntoAcctGA" href="<?php echo esc_attr(ADTONIQ_ACCT_SERVER); ?>">sign in to your Adtoniq Cloud account</a> to
          authorize additional websites and view your bills.
        <?php } ?>
        </p>
      </div>
      <script type="text/javascript">
        AdtoniqGAPlugin.init();
      </script>
    </div>
  <?php
  }

  /**
   * Init function.
   *
   * @since 4.0.0
   */
  function adtoniq_ga_init($apiKey) {
    $this->_apiKey = $apiKey;
    $this->adtoniq_ga_render();
  }

  /**
   * Register settings on init.
   *
   * @since 4.0.0
   */
  function adtoniq_ga_settings() {
    register_setting( 'adtoniq_ga_settings-group', 'adtoniq-ga-property-id' );
    register_setting( 'adtoniq_ga_settings-group', 'adtoniq-ga-traffic-split' );
    register_setting( 'adtoniq_ga_settings-group', 'adtoniq-ga-saved' );
  }

}

$adtoniq_ga = new AdtoniqGA();
