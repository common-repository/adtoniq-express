<?php
/**
 * Adtoniq TruBlock main class
 *
 * @package WordPress
 * @subpackage adtoniq_trublock
 * @since 4.0.0
 */


class AdtoniqTrublock
{

  /**
   *  Options array
   */
  public $options = array();

  /**
   * Build the class
   */
  function __construct() {
    add_action('init', array(&$this, 'get_options'));
    add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
    add_action('admin_init', array(&$this, 'adtoniq_analytics_settings'));
    add_action('admin_menu', array(&$this, 'create_admin_menu'));
  }

  /**
   * Get all option settings
   *
   * @since 4.0.0
   */
  function get_options() {
    $settings = array();
    $this->options = $settings;
  }

  /**
   * Enqueue styles and scripts for plugin.
   *
   * @since  0.1.0
   */
  function admin_enqueue_scripts() {
    wp_enqueue_script('adtoniq-trublock-js', ADTONIQ_PLUGIN_URL . '/js/adtoniq-trublock.js', array(), ADTONIQ_VERSION);
    wp_enqueue_style('adtoniq-trublock-css', ADTONIQ_PLUGIN_URL . '/css/adtoniq-trublock.css', false, ADTONIQ_VERSION);
  }

  /**
   * Add as main menu item
   *
   * @since 4.0.0
   */
  function create_admin_menu() {
    add_menu_page(
      ADTONIQ_PLUGIN_NAME,
      ADTONIQ_PLUGIN_NAME,
      ADTONIQ_CAPABILITY,
      ADTONIQ_PLUGIN_SLUG,
      array(&$this, 'ADTONIQ_init'),
      ADTONIQ_ICON);
  }

  /**
   * Create and add submenu menu item
   *
   * @since 4.0.0
   */
  function create_submenu() {
    add_submenu_page(
      ADTONIQ_SLUG,
      ADTONIQ_PLUGIN_NAME,
      ADTONIQ_SHORTNAME,
      ADTONIQ_CAPABILITY,
      ADTONIQ_PLUGIN_SLUG,
      array(&$this, 'ADTONIQ_init'));
  }

  /**
   * Render main page layout.
   *
   * @since 4.0.0
   */
  function ADTONIQ_render() {
  ?>
    <div class="adtoniq-trublock-plugin wrap">
      <img class="adtoniq-logo" width="250" src="<?php echo ADTONIQ_LOGOPATH; ?>">
      <h2><?php echo ADTONIQ_PLUGIN_NAME; ?> <small class="muted">version <?php echo ADTONIQ_VERSION; ?></small></h2>
      <p>This plugin description.</p>
      <script type="text/javascript">
        window.AdtoniqTrublockPlugin && AdtoniqTrublockPlugin.init();
      </script>
    </div>
  <?php
  }

  /**
   * Init function.
   *
   * @since 4.0.0
   */
  function ADTONIQ_init() {
    $this->ADTONIQ_render();
  }

  /**
   * Register settings on init.
   *
   * @since 4.0.0
   */
  function ADTONIQ_settings() {

  }

}

$adtoniq_trublock = new AdtoniqTrublock();
