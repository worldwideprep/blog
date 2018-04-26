<?php
/**
 * Plugin Name: IntercomWP
 * Plugin URI: https://wordpress.org/plugins/obox-intercom
 * Description: Integrate the <a href="http://intercom.io">Intercom</a> communications app into your WordPress website.
 * Author: Obox
 * Author URI: https://oboxthemes.com
 * Version: 1.1
 * Requires at least: 4.0
 * Tested up to: 4.3
 *
 * Text Domain: intercom_wp
 * Domain Path: /languages/
*/

defined( 'ABSPATH' ) or die();

define( 'INTERCOM_WP_VERSION', '1.1' );

add_action( 'init', 'do_intercom_wp', 10 );
function do_intercom_wp(){
	$intercom_wp = new intercom_wp;
}

class intercom_wp {

	var $admin_form_action;

	var $admin_page_hook;

	var $app_settings;

	var $admin_tabs;

	var $current_admin_tab;

	var $setting_fields;

	const INTERCOM_WP_ADMIN_PAGE = 'intercom-wp';

	function __construct() {

		// Add de/activation hooks
		register_activation_hook( __FILE__, array( $this, 'activation_hook'   ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivation_hook' ) );

		if( FALSE == $this->get_setting( 'app-id' ) ){
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}

		// Register Admin Menu
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Register Settings API
		add_action( 'admin_init', array( $this, 'init_settings' ) );


		// Output Intercom Code
		add_action( 'wp_footer', array( $this, 'intercom_js' ) );
		add_action( 'admin_footer', array( $this, 'intercom_js' ) );

		// Add Admin Notices
		add_action( 'admin_notices', array( $this, 'show_notices' ) );
		add_action( 'network_admin_notices', array( $this, 'show_notices' ) );

		// Add Settings scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts') );

		require_once 'api.php';
	}

	public function admin_notices(){
		global $pagenow;

		if( 'options-general.php' == $pagenow ) return; ?>

		<div class="updated is-dismissible notice">
			<p><?php _e( sprintf( "IntercomWP requires some attention. <a href=\"%s\">Click here</a> to get started.", admin_url( 'options-general.php?page=intercom-wp' ) ), 'intercom_wp' ); ?></p>
		</div>
	<?php }
	/**
	 * Add capabilities when deactivating the plugin
	 */
	function activation_hook() {

		// add the 'hide from intercom' capability to the admin user

		$role = get_role( 'administrator' );
		$role->add_cap( 'hide_intercom' );

	}

	/**
	 * Remove capabilities when deactivating the plugin
	 */
	function deactivation_hook() {

		// remove the 'hide from intercom' capability from the admin user

		$role = get_role( 'administrator' );
		$role->remove_cap( 'hide_intercom' );

	}

	/**
	 * Add Admin menu
	 */
	function show_notices(){
		if ( ! current_user_can( 'manage_options' ) )
			return;
	}

	/**
	 * Add Admin menu
	 */
	function admin_menu() {

		if ( $this->is_network_active() ) {

			$this->admin_page_hook = add_submenu_page(
					'settings.php',
					__( 'Intercom Settings', 'intercom_wp' ),
					'Intercom',
					'manage_network_options',
					'intercom-wp',
					array( $this, 'display_options' )
				);

		} else {

			$this->admin_page_hook = add_options_page(
				__( 'Intercom Settings', 'intercom_wp' ),
				__( 'Intercom', 'intercom_wp' ),
				'manage_options',
				'intercom-wp',
				array( $this, 'display_options' )
			);

		}
	}

	/**
	 * Enqueue admin scripts and styles
	 */
	function enqueue_scripts( $hook ) {

		if ( $this->admin_page_hook != $hook ) {
			return;
		}

		wp_enqueue_script( 'intercom_wp-admin', plugins_url( '/', __FILE__ ) . 'assets/js/admin.js', array( 'jquery' ) );
		wp_enqueue_style( 'intercom_wp-admin', plugins_url( '/', __FILE__ ) . 'assets/css/admin.css'  );
	}

	/**
	 * Send Options
	 */
	function init_settings(){

		$tabs[ 'general' ] = __( 'General' , 'intercom_wp' );

		if( FALSE != $this->get_setting( 'app-id' ) ) {
			$tabs[ 'woocommerce' ] = __( 'WooCommerce <span class="pro">pro</span>' , 'intercom_wp' );
		}

		// Set the tabs available
		$this->admin_tabs = apply_filters( 'intercom_wp_admin_tabs', $tabs );

		// Set the setting fields to use
		$this->setting_fields = apply_filters( 'intercom_wp_admin_inputs',  array(

			'basic-user-header' => array(
					'tab' => 'general',
					'type' => 'heading',
					'title' => __( 'Basic Settings', 'intercom_wp' ),
					'excerpt' => __( 'These are the basic settings required to use Intercom on your site.
						<br /> <br /> Once you have Entered in your App ID, further options will be revealed.' , 'intercom_wp' ),
					'default' => FALSE,
				),

			'app-id' => array(
					'tab' => 'general',
					'type' => 'text',
					'label' => __( 'Intercom App ID', 'intercom_wp' ),
					'description' => ( !$this->get_setting( 'app-id' ) ?
						__( 'Grab your Intercom ID from your intercom URL once logged in.<br /><br /> eg. https://app.intercom.io/a/apps/<strong>zcu2xxxx</strong>/users/segments/active' , 'intercom_wp' )
						: __( 'Click this link to make sure your messenger is active:<a target="_blank" href="https://app.intercom.io/a/apps/'. $this->get_setting( 'app-id' ). '/settings/messenger">https://app.intercom.io/a/apps/'. $this->get_setting( 'app-id' ). '/settings/messenger</a>', 'intercom_wp' )
					),

					'default' => NULL,
					'placeholder' => 'eg. zcu2xxxx'
				),



			'advanced-user-header' => array(
					'tab' => 'general',
					'type' => 'heading',
					'title' => __( 'Advanced Settings', 'intercom_wp' ),
					'excerpt' => __( 'Enter in your Intercom API Key with <strong>Full Access</strong> permissions to enable advanced events.' , 'intercom_wp' ),
					'default' => FALSE,
					'pro' => TRUE,
					'requires' => 'app-id'
				),

			'api-key' => array(
					'tab' => 'general',
					'type' => 'text',
					'label' => __( 'Intercom API Key', 'intercom_wp' ),
					'description' => __( sprintf( 'Grab your API Key from your <a href="%s" target="_blank">Intercom Dashboard</a>', 'https://app.intercom.io/apps/' . $this->get_setting( 'app-id' ) . '/integrations/api_keys' ) , 'intercom_wp' ),
					'default' => NULL,
					'pro' => TRUE,
					'placeholder' => 'eg. 3bc5b2f26ea7959a337e377xxxxxxxxxxa7c3277',
					'requires' => 'app-id'
				),

			'visitors-user-header' => array(
					'tab' => 'general',
					'type' => 'heading',
					'title' => __( 'Site Visitors', 'intercom_wp' ),
					'excerpt' => __( 'These settings are available for anyone who visits your site (non-logged in users).' , 'intercom_wp' ),
					'default' => FALSE,
					'pro' => TRUE,
					'requires' => 'api-key'
				),

			'posts-comment-event' => array(
				'tab' => 'general',
				'type' => 'checkbox',
				'label' => __( 'Track Commenters', 'intercom_wp' ),
				'description' => __( 'Track any user who comments on your site' , 'intercom_wp' ),
				'default' => FALSE,
				'pro' => TRUE,
				'requires' => 'api-key'
			),

			'registered-user-header' => array(
				'tab' => 'general',
				'type' => 'heading',
				'title' => __( 'Registered Users', 'intercom_wp' ),
				'excerpt' => __( 'These settings are available for users who are registered and logged in.' , 'intercom_wp' ),
				'default' => FALSE,
				'requires' => 'app-id'
			),

			'secure-mode' => array(
				'tab' => 'general',
				'type' => 'text',
				'label' => __( 'Secret Key', 'intercom_wp' ),
				'description' => __( 'Required when sending user data. Enable <strong>Secure Mode</strong> by entering in your Intercom Secret Key. <a href="http://docs.intercom.io/configuring-Intercom/enable-secure-mode" target="_blank">More here</a>.' , 'intercom_wp' ),
				'default' => FALSE,
				'requires' => 'app-id'
			),

			'send-user-website' => array(
				'tab' => 'general',
				'type' => 'checkbox',
				'label' => __( 'Website URL', 'intercom_wp' ),
				'description' => __( 'Send users\' website information.' , 'intercom_wp' ),
				'default' => FALSE,
				'requires' => 'app-id'
			),

			'send-user-type' => array(
				'tab' => 'general',
				'type' => 'checkbox',
				'label' => __( 'User Type', 'intercom_wp' ),
				'description' => __( 'Send the type of user you\'re messaging.' , 'intercom_wp' ),
				'default' => FALSE,
				'requires' => 'app-id'
			),

			'send-user-comment-count' => array(
				'tab' => 'general',
				'type' => 'checkbox',
				'label' => __( 'Comment Count', 'intercom_wp' ),
				'description' => __( 'Send users\' comment count.' , 'intercom_wp' ),
				'default' => FALSE,
				'requires' => 'app-id'
			),

			'show-in-admin' => array(
				'tab' => 'general',
				'type' => 'checkbox',
				'label' => __( 'Show in wp-admin', 'intercom_wp' ),
				'description' => __( 'Show Intercom for users in the admin screens.' , 'intercom_wp' ),
				'default' => FALSE,
				'requires' => 'app-id'
			),
		) );

		//die( "<pre>" . print_r( $this->setting_fields , true ) .'</pre>' );

		// Set the form action
		$this->admin_form_action = !is_network_admin() ? 'options-general.php?page=intercom-wp' : 'options.php';

		// If the tab "chosen" is illegal throw an error
		if( isset( $_GET[ 'page' ] ) && self::INTERCOM_WP_ADMIN_PAGE == $_GET[ 'page' ] && isset( $_GET[ 'tab' ] ) ){

			if( !array_key_exists( $_GET[ 'tab' ] , $this->admin_tabs ) ) wp_die( 'You. Shall. Not. Pass' );

			$this->current_admin_tab = $_GET[ 'tab' ];

		} else {

			$this->current_admin_tab = 'general';
		}

		if ( isset( $_REQUEST[ '_wpnonce' ] ) and wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'intercom_wp-options' ) ) {

			$file = is_network_admin() ? 'settings.php' : 'options-general.php';

			if ( isset( $_POST[ 'intercom_wp' ] ) ) {

				// Fetch existing settings
				$original_settings = ( '' == get_option( 'intercom_wp' ) ? array() : get_option( 'intercom_wp' ) );

				// Instantiate an array for the new settings
				$new_settings = array();

				foreach( $this->setting_fields as $setting_key => $setting_options ){

					// Only show the settings that below to this tab
					if( is_array( $setting_options[ 'tab' ] ) ){
						 if( ! in_array( $this->current_admin_tab, $setting_options[ 'tab' ] ) ) continue;
					} else {
						 if( $setting_options[ 'tab' ] != $this->current_admin_tab ) continue;
					}

					$setting_val = isset( $_POST[ 'intercom_wp' ][ $setting_key ] ) ? $_POST[ 'intercom_wp' ][ $setting_key ] : FALSE;

					switch ( $setting_options[ 'type' ] ) {
						case 'checkbox' :
							$setting_val = ( 'on' == $setting_val ? 1: 0 );

							break;

						default :
							$setting_val = esc_attr( $setting_val , array() );

							break;
					}

					$new_settings[ $setting_key ] = ( $setting_val );
				}

				$options_to_save = array_merge( $original_settings, $new_settings );

				update_option( 'intercom_wp' , $options_to_save );
			}

		}

		$this->app_settings = get_option( 'intercom_wp' );

		// Register the App Settings
		register_setting( 'intercom_wp', 'intercom_wp', array( $this, 'validate' ) );

	}

	/**
	 * Send Options
	 */
	function display_options(){ ?>

		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">

			<div class="ip-settings-section">

				<h2><?php _e( 'Intercom WP' , 'intercom_wp' ); ?></h2>

				<form method="post" action="<?php echo $this->admin_form_action; ?>&tab=<?php echo $this->current_admin_tab; ?>" enctype="multipart/form-data" class="launchpad-form">

					<?php settings_fields( 'intercom_wp' ); ?>

					<h2 class="nav-tab-wrapper">
						<?php foreach( $this->admin_tabs as $tab_key => $tab_label) : ?>
							<a href="<?php echo $this->admin_form_action; ?>&tab=<?php echo $tab_key; ?>" class="nav-tab <?php echo $this->current_admin_tab == $tab_key ? 'nav-tab-active' : ''; ?>"><?php echo $tab_label; ?></a>
						<?php endforeach; ?>
					</h2>

					<p class="admin-buttons"><?php echo $this->admin_buttons(); ?></p>

					<?php do_action( 'intercom_wp_pre_settings_form' ); ?>

					<table class="form-table">
						<?php foreach( $this->setting_fields as $setting_key => $setting_options ) {

							if( isset( $setting_options[ 'pro' ] ) && !class_exists( 'intercom_wp_pro' ) ) continue;

							// Only show the settings that below to this tab
							if( is_array( $setting_options[ 'tab' ] ) ){
								 if( ! in_array( $this->current_admin_tab, $setting_options[ 'tab' ] ) ) continue;
							} else {
								 if( $setting_options[ 'tab' ] != $this->current_admin_tab ) continue;
							}

							$input_name = 'intercom_wp[' . $setting_key . ']';
							$input_value = $this->get_setting( $setting_key );

							// Show or hide the input according to the existence of a specific option
							$show_hide = ( (isset( $setting_options[ 'requires' ] ) and !$this->get_setting( $setting_options[ 'requires' ] ) ) ? 'style="display: none;"' : '' ); ?>

							<tr <?php echo $show_hide; ?>>
								<th scope="row">
									<?php // Generate the input label
									if( isset( $setting_options[ 'label' ] ) ){ ?>
										<label for="<?php echo $setting_key; ?>"><?php echo $setting_options[ 'label' ]; ?></label>
									<?php } ?>
								</th>
								<td>
									<?php // Generate the input element
									switch ( $setting_options[ 'type' ] ) {
										case 'button' : ?>
											<a
												class="button-primary"
												href="<?php echo $this->admin_form_action; ?>&action=<?php echo $setting_key; ?>&_wpnonce=<?php echo wp_create_nonce( 'intercom_wp-button' ); ?>&tab=<?php echo $this->current_admin_tab; ?>"
												id="<?php echo $setting_key; ?>"
												name="<?php echo $input_name; ?>">
												<?php echo $setting_options[ 'label' ]; ?>
											</a>
											<?php break;
										case 'checkbox' : ?>
											<input
												type="checkbox"
												id="<?php echo $setting_key; ?>"
												name="<?php echo $input_name; ?>"
												<?php checked( $input_value, TRUE ); ?>
												/>
											<?php break;
										case 'heading' : ?>
											<?php if( isset( $setting_options[ 'title' ] ) ) { ?>
												<h3><?php echo  $setting_options[ 'title' ]; ?></h3>
											<?php } ?>
											<p><?php echo  $setting_options[ 'excerpt' ]; ?></p>
											<?php break;
										default: ?>
										<input
											type="text"
											id="<?php echo $setting_key; ?>"
											name="<?php echo $input_name; ?>"
											value="<?php echo stripslashes( $input_value ); ?>"
											<?php if( isset( $setting_options[ 'placeholder' ] ) ) { ?>
												placeholder="<?php echo $setting_options[ 'placeholder' ]; ?>"
											<?php } ?>
											/>
									<?php } ?>

									<?php // Generate the post-input description
									if( isset( $setting_options[ 'description' ] ) ) { ?>
										<label for="<?php echo $setting_key; ?>"><?php echo $setting_options[ 'description' ]; ?></label>
									<?php } ?>
								</td>
							</tr>

						<?php } ?>
					</table>

					<?php do_action( 'intercom_wp_post_settings_form' ); ?>

					<p class="admin-buttons"><?php echo $this->admin_buttons(); ?></p>

				</form>
			</div>

		</div><!-- /.wrap -->
	<?php }

	static function get_settings(){

		return get_option( 'intercom_wp' );
	}

	function get_setting( $setting_id = NULL ){

		if( !$this->app_settings ){
			$this->app_settings = get_option( 'intercom_wp' );
		}

		if( isset( $this->app_settings[ $setting_id ] ) ) {
			return $this->app_settings[ $setting_id ];
		} else {
			return FALSE;
		}
	}

	function admin_buttons(){ ?>
		<span>
			<a id="clear" href="<?php echo $this->admin_form_action; ?>&refresh=1" class="clear-settings"><?php _e( 'Clear Settings', 'intercom_wp' ); ?></a>
			<button type="submit" class="button-primary"><?php _e( 'Submit', 'intercom_wp' ); ?></button>
		</span>
	<?php }

	/**
	 * Check if the plugin has been activated Network wide
	 */
	function is_network_active() {

		if ( ! function_exists( 'is_plugin_active_for_network' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		if ( is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
			return true;

		return false;

	}

	function intercom_js(){
		global $current_user, $wpdb, $wp_customize;

		// Don't load in the customizer, don't even try it.
		if( isset( $wp_customize ) )
			return;

		if ( current_user_can( 'hide_from_intercom' ) )
			return;

		// don't do anything if the app id and secret key fields have not been set

		$settings = $this->get_settings();

		if ( !isset( $settings[ 'app-id' ] ) or !$settings[ 'app-id' ] )
			return;


		if( is_admin() ){
			if( ( !isset( $settings[ 'show-in-admin' ] ) or FALSE == $settings[ 'show-in-admin' ] ) ){
				return;
			}
		}

		/**
		 * Compile Intercom Settings
		 */
		$js = array();

		// First step, add the App ID
		$js[ 'app_id' ] = $settings[ 'app-id' ];

		// Set the created time
		$js[ 'created_at' ] = current_time( 'timestamp' );

		if( is_user_logged_in() ) {
			wp_get_current_user();

			// Add users' email address
			$js[ 'email' ] = (string) $current_user->user_email;

			// Set the User Name
			$js[ 'name' ] = (string) $current_user->display_name;


			// Set created time to signup time
			$js[ 'created_at' ] = strtotime( $current_user->user_registered );

			// Set the User Role
			if ( isset( $settings[ 'send-user-type' ] ) and $settings[ 'send-user-type' ] ) {
				$user = new WP_User( $current_user->ID );
				if ( !empty( $user->roles ) and is_array( $user->roles ) ) {
					foreach ( $user->roles as $user_role ) {
						$js[ 'User Type' ] = (string) ucfirst( $user_role );
					}
				}
			}

			// Set the User Website
			if ( $settings[ 'send-user-website' ] and isset( $current_user->user_url ) and !empty( $current_user->user_url ) ) {
				$js[ 'Website' ] = (string) $current_user->user_url;
			}

			// Send the Comment Count
			if ( isset( $settings[ 'send-user-comment-count' ] ) and $settings[ 'send-user-comment-count' ] ) {
				$comment_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) AS total FROM $wpdb->comments WHERE comment_approved = 1 AND user_id = %s", $current_user->ID ) );
				$js[ 'Comment Count' ] = (string) $comment_count;
			}

			// calculate the security hash using the user id
			// Enable Secure Mode
			if ( isset( $settings[ 'secure-mode' ] ) and $settings[ 'secure-mode' ] ) {
				$js[ 'user_hash' ] = (string) hash_hmac(
						'sha256',
						$current_user->user_email,
						$settings[ 'secure-mode' ]
					);
			}
		}

		// allow plugins/themes to add their own custom data
		$data = apply_filters( 'intercom_wp_js', $js );

		// jsonify the settings
		$settings_json = json_encode( (object) $data ); ?>
		<script>
			window.intercomSettings = <?php echo $settings_json; ?>;
		</script>
		<script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/<?php echo $settings[ 'app-id' ]; ?>';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>
	<?php
	}

}
