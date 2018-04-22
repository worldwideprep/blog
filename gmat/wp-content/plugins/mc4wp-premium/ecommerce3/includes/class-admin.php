<?php

/**
* Class MC4WP_Ecommerce_Admin
*
* @ignore
*/
class MC4WP_Ecommerce_Admin {

	/**
	* @var string
	*/
	protected $plugin_file;

	/**
	* @var array
	*/
	protected $settings;

	/**
	* @var MC4WP_Queue
	*/
	protected $queue;

	/**
	* MC4WP_Ecommerce_Admin constructor.
	*
	* @param MC4WP_Queue $queue
	* @param string $plugin_file
	* @param array $settings
	*/
	public function __construct( $plugin_file, $queue, $settings ) {
		$this->plugin_file = $plugin_file;
		$this->queue = $queue;
		$this->settings = $settings;

		// Don't typehint $queue in constructor as this may be null when e-commerce is disabled
	}

	/**
	* Add hooks
	*/
	public function add_hooks() {
		add_filter( 'mc4wp_admin_menu_items', array( $this, 'menu_items' ) );
		add_action( 'mc4wp_admin_save_ecommerce_settings', array( $this, 'save_settings' ) );
		add_action( 'mc4wp_admin_ecommerce_reset', array( $this, 'reset_data' ) );
		add_action( 'mc4wp_admin_ecommerce_rollback_to_v2', array( $this, 'rollback_to_v2' ) );
	}

	/**
	* Rolls back to e-commerce on API v2.
	*/
	public function rollback_to_v2() {
		// re-enable old option
		$options = get_option( 'mc4wp', array() );
		$options['ecommerce'] = 1;
		update_option( 'mc4wp', $options );

		// delete new option
		delete_option( 'mc4wp_ecommerce' );

		// redirect to wizard
		wp_redirect( admin_url('admin.php?page=mailchimp-for-wp-other' ) );
		exit;
	}

	/**
	* Runs logic for saving e-commerce settings & wizard.
	*/
	public function save_settings() {
		// check if queue processor is scheduled
		_mc4wp_ecommerce_schedule_events();
		
		$ecommerce = $this->get_ecommerce();
		$messages = $this->get_admin_messages();

		check_admin_referer( 'save_ecommerce_settings' );
		$dirty = stripslashes_deep( $_POST['mc4wp_ecommerce'] );

		// merge with current settings to allow passing partial arrays
		$current = $this->settings;
		$dirty = array_replace_recursive( $current, $dirty );
		$diff = array_diff( $dirty['store'], $current['store'] );

		// force update store when load_mcjs_script is enabled but mcjs url is empty
		if( $dirty['load_mcjs_script'] && ( empty( $dirty['mcjs_url'] ) || $current['load_mcjs_script'] == 0 ) ) {
			$diff = true;
		}

		if( ! empty( $diff ) ) {
			try {
				$store_data = $ecommerce->update_store( $dirty['store'] );
			} catch( Exception $e ) {
				$messages->flash( (string) $e, 'error' );
				$_POST['_redirect_to'] = '';
				return; // return means we're not saving
			}

			// store actual store ID + mc.js script url
			$dirty['store_id'] = $store_data->id;
			$dirty['mcjs_url'] = $store_data->connected_site->site_script->url;
			$ecommerce->set_store_id( $store_data->id );
		}

		// verify script installation after it is toggled
		if( $dirty['load_mcjs_script'] == 1 && $current['load_mcjs_script'] == 0) {
			try{
				$ecommerce->verify_store_script_installation();
			} catch( MC4WP_API_Resource_Not_Found_Exception $e ) {
				// error verifying script installation (store not found)
				$messages->flash( "Error enabling MC.js. Please re-connect your store to MailChimp.", 'error' );
				return; // return means we're not saving
			}
		}

		// save new settings if something changed
		if( $dirty != $current || ! empty( $diff ) ) {
			update_option( 'mc4wp_ecommerce', $dirty );
			$messages->flash( 'Settings saved!' );
		}

		
	}

	/**
	* @param array $items
	*
	* @return array
	*/
	public function menu_items( $items ) {
		$items[ 'ecommerce' ] = array(
			'title' => __( 'E-Commerce', 'mc4wp-ecommerce' ),
			'text' => __( 'E-Commerce', 'mc4wp-ecommerce' ),
			'slug' => 'ecommerce',
			'callback' => array( $this, 'show_settings_page' ),
			'load_callback', array( $this, 'redirect_to_wizard' ),
		);

		return $items;
	}

	/**
	* Redirect to wizard when store settings are empty.
	*/
	public function redirect_to_wizard() {
		$settings = $this->settings;

		if( $settings['enable_object_tracking'] && empty( $settings['store']['list_id'] ) && ! isset( $_GET['wizard'] ) ) {
			wp_safe_redirect( add_query_arg( array( 'wizard' => 1 ) ) );
		}
	}

	/**
	* Show settings page
	*/
	public function show_settings_page() {
		$settings = $this->settings;
		$mailchimp = new MC4WP_MailChimp();
		$lists = $mailchimp->get_lists();
		$connected_list = null;

		$helper = new MC4WP_Ecommerce_Helper();
		$product_count = new MC4WP_Ecommerce_Object_Count( $helper->get_product_count( false ), $helper->get_product_count( true ) );
		$order_count = new MC4WP_Ecommerce_Object_Count( $helper->get_order_count( false ), $helper->get_order_count( true ) );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$assets_url = plugins_url( '/assets', $this->plugin_file );
		wp_enqueue_style( 'mc4wp-ecommerce-admin', $assets_url . '/css/admin' . $suffix . '.css', array(), MC4WP_PREMIUM_VERSION );
		wp_enqueue_script( 'mc4wp-ecommerce-admin', $assets_url . '/js/admin' . $suffix . '.js', array(), MC4WP_PREMIUM_VERSION, true );
		wp_localize_script( 'mc4wp-ecommerce-admin', 'mc4wp_ecommerce', array(
			'i18n' => array(
				'done' => __( 'All done!', 'mc4wp-ecommerce' ),
				'pause' => __( 'Pause', 'mc4wp-ecommerce' ),
				'resume' => __( 'Resume', 'mc4wp-ecommerce' ),
				'confirmation' => __( 'Are you sure you want to do this?', 'mc4wp-ecommerce' ),
				'process' => __( 'Process', 'mc4wp-ecommerce' ),
				'processing' => __( 'Processing queue, please wait.', 'mc4wp-ecommerce' ),
			),
			'product_count' => $product_count,
			'product_ids' => $product_count->untracked > 0 ? $helper->get_untracked_product_ids() : array(),
			'order_count' => $order_count,
			'order_ids' => $order_count->untracked > 0 ? $helper->get_order_ids( true ) : array(),
		));

		// get connected list
		if( ! empty( $settings['store']['list_id'] ) ) {
			$connected_list = $mailchimp->get_list( $settings['store']['list_id'] );
		}

		$queue = $this->queue;

		if( isset( $_GET['edit'] ) && $_GET['edit'] === 'store' ) {
			require __DIR__ . '/views/edit-store.php';
		} else if( ! empty( $_GET['wizard'] ) ) {
			require __DIR__ . '/views/wizard.php';
		} else {
			require __DIR__ . '/views/admin-page.php';
		}
	}

	/**
	* Resets all e-commerce data
	*/
	public function reset_data() {
		$this->settings['store']['list_id'] = '';
		update_option( 'mc4wp_ecommerce', $this->settings );

		// delete local tracking indicators
		delete_post_meta_by_key( MC4WP_Ecommerce::META_KEY );

		// remove store in mailchimp
		try {
			$this->get_api()->delete_ecommerce_store( $this->settings['store_id'] );
		} catch( MC4WP_API_Resource_Not_Found_Exception $e ) {
			// good.
		} catch( Exception $e ) {
			// bad.
			$this->get_admin_messages()->flash( (string) $e, 'error' );
			return;
		}

        $this->settings['store_id'] = '';
        update_option( 'mc4wp_ecommerce', $this->settings );
	}

	/**
	* @return MC4WP_API_v3
	*/
	private function get_api() {
		return mc4wp('api');
	}

	/**
	* @return MC4WP_Ecommerce
	*/
	private function get_ecommerce() {
		return mc4wp('ecommerce');
	}

	/**
	* @return MC4WP_Admin_Messages
	*/
	private function get_admin_messages() {
		return mc4wp('admin.messages');
	}
}
