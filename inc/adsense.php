<?php
/**
 * Adtoniq Adsense main class
 *
 * @package WordPress
 * @subpackage adtoniq_adsense
 * @since 4.0.0
 */


class AdtoniqAdSense
{

  private
    $_apiKey,
    $publisherId,
    $maxAds,
    $enabled;

  /**
   * Build the class
   */
  function __construct() {
    // add_action('init', array(&$this, 'get_options'));
    // add_action('admin_init', array(&$this, 'adtoniq_adsense_settings'));
  }

  /**
   * Get all option settings
   *
   * @since 4.0.0
   */
  function get_options() {
    // any options to add -- put them here
  }


  /* Return JavaScript for AdSense */
  function javascript() {
    $result = "";
    if (get_option('adtoniq-as-enabled') == 'on') {
      $result .= "<script>if (adtoniq) {";
      $proxyServer = get_option('adtoniq-debug-proxy-server', '');
      $cssSelector = get_option('adtoniq-as-css-selector', "ins,[id^='div-gpt-ad']");
      $filter = get_option('adtoniq-debug-proxy-filter', '');
      if (strlen($filter) > 0)
        $result .= "if (adtoniq.setFilter) adtoniq.setFilter('" . $filter . "');";
      if (strlen($cssSelector) == 0)
        $cssSelector = "ins,[id^='div-gpt-ad']";

      $cssSelector = str_replace('"', '\'', $cssSelector);
      adtoniq_add_event("CSS Selector is: " . $cssSelector);
      $result .= "adtoniq.setAdUnitLocator(function() {return document.querySelectorAll(\"" . $cssSelector . "\")}); // 1";

      if (strlen($proxyServer) > 0) {
        $result .= "
        var ps = '" . $proxyServer . "';";
      } else {
        $result .= "
          var ps = adtoniq.getProxy();
        ";
      }
      $result .= "
        adtoniq.onBlocked(function(adtoniqCookie, proxyServer) {
          function doit() {
            adtoniq.inflateAdUnit('ins', '" . get_option('adtoniq-as-publisher-id') . "', '" . get_option('adtoniq-as-max-ads')
             . "', '" . get_option('adtoniq-as-data-ad-layout')
             . "', '" . get_option('adtoniq-as-data-ad-format')
             . "', '" . get_option('adtoniq-as-data-ad-slot')
            . "'
            );
          }
          if (adtoniqCookie != 'track') {
            adtoniq.onOptIn(doit);
          } else {
            doit();
          }";

      $result .= "
      });}</script>";
    }
    return $result;
  }
  
  function adtoniq_as_extract_attributes() {
    $codeSnippet = get_option("adtoniq-as-code-snippet");
    $this->adtoniq_as_extract_attribute($codeSnippet, "data-ad-layout");
    $this->adtoniq_as_extract_attribute($codeSnippet, "data-ad-format");
    $this->adtoniq_as_extract_attribute($codeSnippet, "data-ad-client");
    $this->adtoniq_as_extract_attribute($codeSnippet, "data-ad-slot");
  }
  
  function adtoniq_as_extract_attribute($codeSnippet, $name) {
    $startName = strpos($codeSnippet, $name);
    $value = '';
    if ($startName !== false) {
      $startValue = strpos($codeSnippet, "\"", $startName) + 1;
      $endValue = strpos($codeSnippet, "\"", $startValue);
      $length = $endValue - $startValue;
      $value = substr($codeSnippet, $startValue, $length);
    }
    
    adtoniq_update_option('adtoniq-as-' . $name, $value, true);
    adtoniq_add_event('Set adtoniq-as-' . $name . ' to ' . $value);
  }

  /**
   * Show save modal
   *
   * @since 4.0.0
   */
  function adtoniq_as_render_update() {
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
  function adtoniq_adsense_render() {
    if ($this->saved === 'saved') {
      $this->adtoniq_as_render_update();
      adtoniq_update_option('adtoniq-as-saved', '', true);
      $this->adtoniq_as_extract_attributes();
    }
  ?>
    <div class="well" data-help="afas">
      <h1 class="page-header">
        Google AdSense Settings (advanced beta 4)
        <small class="muted right">Use Google AdSense</small>
      </h1>
      <div class="well-container">
        <p class="lead">
        <?php if (strlen($this->_apiKey) === 0) { ?>
          This feature requires an Adtoniq Cloud account. <a id="signUpAcctAdsense" href="<?php echo esc_attr(ADTONIQ_ACCT_SERVER); ?>?source=getkey">Sign up for an Adtoniq
          Cloud account</a> and receive one month free. No credit card is required unless you
          want to continue using Adtoniq server features after your one month trial.
        <?php } else { ?>
          <form class="adtoniq-ga-form form-horizontal" id="AdtoniqASForm" method="post" action="options.php">
            <?php settings_fields( 'adtoniq_as_settings-group' ); ?>
            <?php wp_nonce_field( 'register_adtoniq', 'register_adtoniq_field' ); ?>
            <?php do_settings_sections( 'adtoniq_as_settings-group' ); ?>
            <input type="hidden" name="adtoniq-as-saved" value="saved" />
            <div class="form-group">
              <label for="adtoniq-as-publisher-id" class="col-sm control-label">
                AdSense Publisher ID
              </label>
              <div class="col-sm">
                <input
                  type="text"
                  class="form-control"
                  id="asPublisherId"
                  name="adtoniq-as-publisher-id"
                  placeholder="pub-xxxxxxxxxxxxxxxx"
                  value="<?php echo esc_attr( $this->publisherId ); ?>"
                  tabindex="1"
                  aria-describedby="asPublisherHelpBlock">
                  <span class="form-control-feedback" aria-hidden="true"></span>
              </div>
              <span id="asPublisherHelpBlock" class="help-block col-xs-offset">Message Here</span>
            </div>

            
            <div class="form-group">
              <label for="adtoniq-as-code-snippet" class="col-sm control-label">
                AdSense ad unit code snippet
              </label>
              <div class="col-sm">
                <textarea rows="12" cols="70"
                  class=""
                  id="asCodeSnippet"
                  name="adtoniq-as-code-snippet"
                  tabindex="2"
                  aria-describedby="asCodeSnippetHelpBlock"
                  placeholder=""><?php echo esc_attr( $this->adUnitCodeSnippet ); ?></textarea>
                <span class="form-control-feedback" aria-hidden="true"></span>
              </div>
              <span id="asCodeSnippetHelpBlock" class="help-block col-xs-offset">Message Here</span>
            </div>

            
            <div class="form-group">
              <label for="adtoniq-css-selector" class="col-sm control-label">
                CSS Selector
              </label>
              <div class="col-sm">
                <input
                  type="text"
                  class="form-control"
                  id="asCssSelector"
                  name="adtoniq-as-css-selector"
                  placeholder=""
                  value="<?php echo esc_attr( $this->cssSelector ); ?>"
                  tabindex="3"
                  aria-describedby="asCssHelpBlock">
                  <span class="form-control-feedback" aria-hidden="true"></span>
              </div>
              <span id="asCssHelpBlock" style="display:initial;" class="help-block col-xs-offset">Default if blank: ins,[id^='div-gpt-ad']</span>
            </div>
            
            
            <div class="form-group">
              <label for="adtoniq-as-max-ads" class="col-sm control-label">
                Maximum Ad Units
              </label>
              <div class="col-sm">
                <input
                  type="number"
                  class="form-control"
                  id="asMaxAds"
                  name="adtoniq-as-max-ads"
                  value="<?php echo esc_attr( $this->maxAds ); ?>"
                  tabindex="4"
                  min="0"
                  max="100"
                  step="1"
                  aria-describedby="asMaxAdsHelpBlock">
                  <span class="form-control-feedback" style="right:30px" aria-hidden="true"></span>
              </div>
              <span id="asMaxAdsHelpBlock" style="display:initial;" class="help-block col-xs-offset">Default if blank: ins,[id^='div-gpt-ad']</span>
            </div>
            <div class="form-group">
              <div class="switch">
                <label for="adtoniq-as-enabled" class="col-sm control-label">
                  Enable adblock bypass
                </label>
                <div class="col-">
                <input name="adtoniq-as-enabled" id="adtoniqAsEnabled" class="cmn-toggle cmn-toggle-round" type="checkbox" tabindex="1"
                  <?php if ($this->enabled == 'on') echo 'checked'; ?> />
                  <label for="adtoniqAsEnabled"></label>
                </div>
                <div class="col-sm vert-center">
                  <p>Bypass adblockers with user consent</p>
                </div>
              </div>
            </div>
            <div class="form-group">
              <input type="submit" name="submit" id="submit" class="btn btn-primary btn-block" value="Save Changes" tabindex="1">
            </div>
          </form>
          <p>
          Thank you for using Adtoniq. You can <a id="signIntoAcctAdsense" href="<?php echo esc_attr(ADTONIQ_ACCT_SERVER); ?>">sign in to your Adtoniq Cloud account</a> to
          authorize additional websites and view your bills.
        <?php } ?>
        </p>
      </div>
      <script type="text/javascript">
        AdtoniqAdsensePlugin.init();
      </script>
    </div>
  <?php
  }

  /**
   * Init function.
   *
   * @since 4.0.0
   */
  function adtoniq_adsense_init($apiKey) {
    $this->_apiKey = $apiKey;
    $this->publisherId = get_option('adtoniq-as-publisher-id');
    $this->maxAds = get_option('adtoniq-as-max-ads');
    $this->enabled = get_option('adtoniq-as-enabled');
    $this->saved = get_option('adtoniq-as-saved');
    $this->cssSelector = get_option('adtoniq-as-css-selector');
    $this->adUnitCodeSnippet = get_option('adtoniq-as-code-snippet');
    $this->adtoniq_adsense_render();
  }

  /**
   * Register settings on init.
   *
   * @since 4.0.0
   */

  function adtoniq_adsense_settings() {
    register_setting( 'adtoniq_as_settings-group', 'adtoniq-as-enabled' );
    register_setting( 'adtoniq_as_settings-group', 'adtoniq-as-max-ads' );
    register_setting( 'adtoniq_as_settings-group', 'adtoniq-as-publisher-id' );
    register_setting( 'adtoniq_as_settings-group', 'adtoniq-as-saved' );
    register_setting( 'adtoniq_as_settings-group', 'adtoniq-as-css-selector' );
    register_setting( 'adtoniq_as_settings-group', 'adtoniq-as-code-snippet' );
    register_setting( 'adtoniq_as_settings-group', 'adtoniq-as-data-ad-layout' );
    register_setting( 'adtoniq_as_settings-group', 'adtoniq-as-data-ad-format' );
    register_setting( 'adtoniq_as_settings-group', 'adtoniq-as-data-ad-client' );
    register_setting( 'adtoniq_as_settings-group', 'adtoniq-as-data-ad-slot' );
  }
}

$adtoniq_adsense = new AdtoniqAdSense();
