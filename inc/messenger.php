<?php
/**
 * Adtoniq Messenger main class
 *
 * @package WordPress
 * @subpackage adtoniq_msg
 * @since 4.0.0
 */

class AdtoniqMessenger
{

  /**
   *  Options array
   */
  public $options = array();

  /**
   *  Default settings for the alert
   *
   * @since  0.1.0
   */
  public $alert_settings_default = array(
    'greeting' => '<p style="padding-right:20px;">We use analytics on our website to improve our services. Would you like to opt in to using analytics?</p>',
    'confirm' => '',
    'reject' => '',
    'confirm_btn' => 'Yes',
    'reject_btn' => 'No'
  );

  /**
   *  Settings for the text areas
   *
   * @since  0.1.0
   */
  public $settings_greeting_mce_field = array(
      'teeny' => true,
      'textarea_rows' => 6,
      'tabindex' => 1,
      'textarea_name' => 'adtoniq-msg-message',
      'wpautop' => true,
      'media_buttons' => false,
      'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
      'tinymce' => array(
        'toolbar1' => 'bold,italic,alignleft,aligncenter,alignright,link,unlink'
      )
  );

  public $settings_confirm_mce_field = array(
      'teeny' => true,
      'textarea_rows' => 6,
      'tabindex' => 1,
      'textarea_name' => 'adtoniq-msg-confirm',
      'wpautop' => true,
      'media_buttons' => false,
      'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
      'tinymce' => array(
        'toolbar1' => 'bold,italic,alignleft,aligncenter,alignright,link,unlink'
      )
  );

  public $settings_reject_mce_field = array(
      'teeny' => true,
      'textarea_rows' => 6,
      'tabindex' => 1,
      'textarea_name' => 'adtoniq-msg-reject',
      'wpautop' => true,
      'media_buttons' => false,
      'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
      'tinymce' => array(
        'toolbar1' => 'bold,italic,alignleft,aligncenter,alignright,link,unlink'
      )
  );

  /**
   * Build the class
   */
  function __construct() {
    add_action('init', array(&$this, 'get_options'));
    // add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
    add_action('admin_init', array(&$this, 'adtoniq_msg_settings'));
  }

  /**
   * Get all option settings
   *
   * @since 4.0.0
   */
  function get_options() {
    $settings = array();
    $settings['enabled'] = get_option('adtoniq-msg-is-enabled');
    $settings['targetedUsers'] = get_option('adtoniq-msg-users');
    $settings['message'] = get_option('adtoniq-msg-message') ?
      get_option('adtoniq-msg-message') : $this->alert_settings_default['greeting'];
    $settings['confirm'] = get_option('adtoniq-msg-confirm') ?
      get_option('adtoniq-msg-confirm') : $this->alert_settings_default['confirm'];
    $settings['reject'] = get_option('adtoniq-msg-reject') ?
      get_option('adtoniq-msg-reject') : $this->alert_settings_default['reject'];
    $settings['confirm_btn'] = get_option('adtoniq-msg-confirm-btn') ?
      get_option('adtoniq-msg-confirm-btn') : $this->alert_settings_default['confirm'];
    $settings['reject_btn'] = get_option('adtoniq-msg-reject-btn') ?
      get_option('adtoniq-msg-reject-btn') : $this->alert_settings_default['reject'];
    $settings['custom_class'] = get_option('adtoniq-msg-custom-btn-class');
    $settings['select'] = get_option('adtoniq-msg-select');
    $this->options = $settings;
  }

  /**
   * Enqueue styles and scripts for plugin.
   *
   * @since  0.1.0
   */
  function admin_enqueue_scripts() {
    wp_enqueue_script('adtoniq-msg-js', ADTONIQ_PLUGIN_URL . '/js/adtoniq-messenger.js', array(), ADTONIQ_VERSION);
    wp_enqueue_script('bs-tabs', get_template_directory_uri() .'/js/vendor/bs-tabs.js', array('jquery'), null, true);
  }

  /**
   * Display alert on save.
   *
   * @since 4.0.0
   */
  function adtoniq_msg_render_update() {
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
  function adtoniq_msg_render() {
  ?>
    <div class="well" data-help="messaging">
      <h1 class="page-header">
        Messaging
        <small class="muted right">Communicate with your adblocked audience</small>
      </h1>
      <div class="well-container">
        <form action="options.php" method="post" id="AdtoniqMessengerForm" class="adtoniq-form form-horizontal">
          <?php settings_fields('adtoniq-msg-settings-group'); ?>
          <?php do_settings_sections('adtoniq-msg-settings-group'); ?>
      		<input type="hidden" name="adtoniq-msg-saved" value="saved" />
          <div>
            <div class="form-group">
              <div class="switch">
                <label for="adtoniqMsgEnabled" class="col-sm control-label">
                  Enable alert
                </label>
                <div class="col-sm">
                <input name="adtoniq-msg-is-enabled" id="adtoniqMsgEnabled" class="cmn-toggle cmn-toggle-round" type="checkbox"
                  <?php echo (empty($this->options['enabled'])) ? array() : ($this->options['enabled'] ? 'checked' : ''); ?> />
                  <label for="adtoniqMsgEnabled"></label>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm control-label">
                Show the message for:
              </label>
              <div class="col-">
                <div class="radio">
                  <label>
                    <input type="radio" id="adtoniq-msg-users-all" name="adtoniq-msg-users" value="all"
                      <?php echo (empty($this->options['targetedUsers'])) ? 'selected' : ($this->options['targetedUsers'] === 'all' ? 'checked' : ''); ?> />
                    All users
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" id="adtoniq-msg-users-adsonly" name="adtoniq-msg-users" value="adsonly"
                    <?php echo (empty($this->options['targetedUsers'])) ? '': ($this->options['targetedUsers'] === 'adsonly' ? 'checked' : ''); ?> />
                    Users blocking ads
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" id="adtoniq-msg-users-adsplus" name="adtoniq-msg-users" value="adsplus"
                    <?php echo (empty($this->options['targetedUsers'])) ? '' : ($this->options['targetedUsers'] === 'adsplus' ? 'checked' : ''); ?> />
                    Users blocking ads and analytics
                  </label>
                </div>
              </div>
              <div class="col-sm">
                Show the greeting message for the type of users you select, along with buttons to opt in or opt out of your choice. The message is displayed until the user makes a choice, after which a cookie is dropped to record their choice and they will no longer see the message.
              </div>
            </div>
          </div>
          <fieldset id="adtoniqMsgMessageFields">
            <legend>Message Options</legend>
              <div class="form-group">
                <label for="adtoniqMsgSelector" id="adtoniqMsgMessageSelector" class="col-sm control-label">
                  Select message to edit:
                </label>
                <div class="col-sm">
                  <select name="adtoniq-msg-select" class="form-control" id="adtoniqMsgSelector">
                    <option <?php echo $this->options['select'] === 'greeting' ? 'selected' : ''; ?> value="greeting">Greeting message</option>
                    <option <?php echo $this->options['select'] === 'confirm' ? 'selected' : ''; ?> value="confirm">Confirm messsge</option>
                    <option <?php echo $this->options['select'] === 'reject' ? 'selected' : ''; ?> value="reject">Reject message</option>
                  </select>
                </div>
              </div>
              <div class="adtoniq-mce-container" id="amsgGreeting">
                <label for="adtoniqMsgMessage">Greeting Message:</label>
                <?php
                  wp_editor($this->options['message'], 'adtoniqMsgMessage', $this->settings_greeting_mce_field);
                ?>
              </div>
              <div class="adtoniq-mce-container hidden" id="amsgConfirm">
                <label for="adtoniqMsgConfirm">Confirm Message:</label>
                <?php
                  wp_editor($this->options['confirm'], 'adtoniqMsgConfirm', $this->settings_confirm_mce_field);
                ?>
              </div>
              <div class="adtoniq-mce-container hidden" id="amsgReject">
                <label for="adtoniqMsgReject">Reject Message:</label>
                <?php
                  wp_editor($this->options['reject'], 'adtoniqMsgReject', $this->settings_reject_mce_field);
                ?>
              </div>
            </legend>
          </fieldset>
          <fieldset id="adtoniqMsgButtonFields">
            <legend>Button Options</legend>
              <div class="form-group">
                <label for="adtoniqMsgConfirmBtnText" class="col-sm control-label">
                  Confirm button text
                </label>
                <div class="col-sm">
                  <input
                    type="text"
                    class="form-control"
                    id="adtoniqMsgConfirmBtnText"
                    name="adtoniq-msg-confirm-btn"
                    placeholder="Enter text for confirm button"
                    value="<?php echo $this->options['confirm_btn']; ?>"
                    tabindex="1"
                    aria-describedby="buttonConfirmHelpBlock" />
                    <span class="form-control-feedback" aria-hidden="true"></span>
                </div>
                <div class="col-sm vert-center">
                  <p>Word that appears in your "yes, please track me" button. <strong>Is optional.</strong></p>
                </div>
                <span id="buttonConfirmHelpBlock" class="help-block col-xs-offset">Message Here</span>
              </div>
              <div class="form-group">
                <label for="adtoniqMsgRejectBtnText" class="col-sm control-label">
                  Reject button text
                </label>
                <div class="col-sm">
                  <input
                    type="text"
                    class="form-control"
                    id="adtoniqMsgRejectBtnText"
                    name="adtoniq-msg-reject-btn"
                    placeholder="Enter text for reject button"
                    value="<?php echo $this->options['reject_btn']; ?>"
                    tabindex="1"
                    aria-describedby="buttonRejectHelpBlock">
                    <span class="form-control-feedback" aria-hidden="true"></span>
                </div>
                <div class="col-sm vert-center">
                  <p>Word that appears in your "no, do not track me" button. <strong>Is optional. If you leave this out, users can only opt in.</strong></p>
                </div>
                <span id="buttonRejectHelpBlock" class="help-block col-xs-offset">Message Here</span>
              </div>
              <div class="form-group">
                <label for="adtoniqMsgCustomBtnText" class="col-sm control-label">
                  Custom button class
                </label>
                <div class="col-sm">
                  <input
                    type="text"
                    class="form-control"
                    id="adtoniqMsgCustomBtnText"
                    name="adtoniq-msg-custom-btn-class"
                    placeholder="Enter name of custom class"
                    value="<?php echo $this->options['custom_class']; ?>"
                    tabindex="1" />
                    <span class="form-control-feedback" aria-hidden="true"></span>
                </div>
                <div class="col-sm vert-center">
                  <p>Add this class to your button to target for custom styling. Optional.</p>
                </div>
              </div>
            </legend>
          </fieldset>
          <div class="form-group">
            <input type="submit" name="submit" id="submit" class="btn btn-primary btn-block" value="Save Changes">
          </div>
      	</form>
      </div>
      <script type="text/javascript">
        window.AdtoniqMessengerPlugin && AdtoniqMessengerPlugin.init();
      </script>
    </div>
  <?php
  }

  /**
   * Init function.
   *
   * @since 4.0.0
   */
  function adtoniq_msg_init() {
    $this->adtoniq_msg_render();
    if (get_option('adtoniq-msg-saved') === 'saved') {
      $this->adtoniq_msg_render_update();
      adtoniq_update_option('adtoniq-msg-saved', '', true);
    }
  }

  /**
   * Register settings on init.
   *
   * @since 4.0.0
   */
  function adtoniq_msg_settings() {
    register_setting('adtoniq-msg-settings-group', 'adtoniq-msg-saved');
    register_setting('adtoniq-msg-settings-group', 'adtoniq-msg-is-enabled');
    register_setting('adtoniq-msg-settings-group', 'adtoniq-msg-users');
    register_setting('adtoniq-msg-settings-group', 'adtoniq-msg-message');
    register_setting('adtoniq-msg-settings-group', 'adtoniq-msg-confirm');
    register_setting('adtoniq-msg-settings-group', 'adtoniq-msg-reject');
    register_setting('adtoniq-msg-settings-group', 'adtoniq-msg-confirm-btn');
    register_setting('adtoniq-msg-settings-group', 'adtoniq-msg-reject-btn');
    register_setting('adtoniq-msg-settings-group', 'adtoniq-msg-custom-btn-class');
    register_setting('adtoniq-msg-settings-group', 'adtoniq-msg-select');
 }

}

$adtoniq_msg = new AdtoniqMessenger();
