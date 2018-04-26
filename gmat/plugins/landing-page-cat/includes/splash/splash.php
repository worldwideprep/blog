<?php
	
function fca_lpc_splash_page() {
	add_submenu_page(
		null,
		__('Activate', 'landing-page-cat'),
		__('Activate', 'landing-page-cat'),
		'manage_options',
		'landing-page-cat-splash',
		'fca_lpc_render_splash_page'
	);
}
add_action( 'admin_menu', 'fca_lpc_splash_page' );

function fca_lpc_render_splash_page() {
		
	wp_enqueue_style('fca_lpc_splash_css', FCA_LPC_PLUGINS_URL . '/includes/splash/splash.min.css', false, FCA_LPC_PLUGIN_VER );
	wp_enqueue_script('fca_lpc_splash_js', FCA_LPC_PLUGINS_URL . '/includes/splash/splash.min.js', false, FCA_LPC_PLUGIN_VER, true );
		
	$user = wp_get_current_user();
	$name = empty( $user->user_firstname ) ? '' : $user->user_firstname;
	$email = $user->user_email;
	$site_link = '<a href="' . get_site_url() . '">'. get_site_url() . '</a>';
	$website = get_site_url();
	$nonce = wp_create_nonce( 'fca_lpc_activation_nonce' );
	
	echo '<form method="post" action="' . admin_url( '/admin.php?page=landing-page-cat-splash' ) . '">';
		echo '<div id="fca-logo-wrapper">';
			echo '<div id="fca-logo-wrapper-inner">';
				echo '<img id="fca-logo-text" src="' . FCA_LPC_PLUGINS_URL . '/assets/fatcatapps-logo-text.png' . '">';
			echo '</div>';
		echo '</div>';
		
		echo "<input type='hidden' name='fname' value='$name'>";
		echo "<input type='hidden' name='email' value='$email'>";
		echo "<input type='hidden' name='fca-lpc-nonce' value='$nonce'>";
		
		echo '<div id="fca-splash">';
			echo '<h1>' . __( 'Welcome to Landing Page Cat', 'landing-page-cat' ) . '</h1>';
			
			echo '<div id="fca-splash-main" class="fca-splash-box">';
				echo '<p id="fca-splash-main-text">' .  sprintf ( __( 'In order to enjoy all our features and functionality, Landing Page Cat needs to connect your user, %1$s at %2$s, to <strong>api.fatcatapps.com</strong>.', 'landing-page-cat' ), '<strong>' . $name . '</strong>', '<strong>' . $website . '</strong>'  ) . '</p>';
				echo "<button type='submit' id='fca-lpc-submit-btn' class='fca-lpc-button button button-primary' name='fca-lpc-submit-optin' >" . __( 'Connect', 'landing-page-cat') . "</button><br>";
				echo "<button type='submit' id='fca-lpc-optout-btn' name='fca-lpc-submit-optout' >" . __( 'Skip This Step', 'landing-page-cat') . "</button>";
			echo '</div>';
			
			echo '<div id="fca-splash-permissions" class="fca-splash-box">';
				echo '<a id="fca-splash-permissions-toggle" href="#" >' . __( 'What permission is being granted?', 'landing-page-cat' ) . '</a>';
				echo '<div id="fca-splash-permissions-dropdown" style="display: none;">';
					echo '<h3>' .  __( 'Your Website Info', 'landing-page-cat' ) . '</h3>';
					echo '<p>' .  __( 'Your URL, WordPress version, plugins & themes.', 'landing-page-cat' ) . '</p>';
					
					echo '<h3>' .  __( 'Your Info', 'landing-page-cat' ) . '</h3>';
					echo '<p>' .  __( 'Your name and email.', 'landing-page-cat' ) . '</p>';
					
					echo '<h3>' .  __( 'Plugin Usage', 'landing-page-cat' ) . '</h3>';
					echo '<p>' .  __( 'How you use Landing Page Cat.', 'landing-page-cat' ) . '</p>';				
				echo '</div>';
			echo '</div>';
			

		echo '</div>';
	
	echo '</form>';
	
	echo '<div id="fca-splash-footer">';
		echo '<a target="_blank" href="https://fatcatapps.com/legal/terms-service/">' . _x( 'Terms', 'as in terms and conditions', 'landing-page-cat' ) . '</a> | <a target="_blank" href="https://fatcatapps.com/legal/privacy-policy/">' . _x( 'Privacy', 'as in privacy policy', 'landing-page-cat' ) . '</a>';
	echo '</div>';
}

function fca_lpc_admin_redirects() {
	if ( isset( $_POST['fca-lpc-nonce'] ) ) {
		
		$nonce_verified = wp_verify_nonce( $_POST['fca-lpc-nonce'], 'fca_lpc_activation_nonce' ) == 1;
		
		if ( isset( $_POST['fca-lpc-submit-optout'] ) && $nonce_verified ) {
			update_option( 'fca_lpc_activation_status', 'disabled' );
			wp_redirect( admin_url( '/edit.php?post_type=landingpage' ) );
			exit;
		} else if ( isset( $_POST['fca-lpc-submit-optin'] ) && $nonce_verified ) {
			update_option( 'fca_lpc_activation_status', 'active' );
			$email = urlencode ( sanitize_email ( $_POST['email'] ) );
			$name = urlencode ( sanitize_text_field ( $_POST['fname'] ) );
			$product = 'landingpagecat';
			$url =  "https://api.fatcatapps.com/api/activate.php?email=$email&fname=$name&product=$product";
			$return = wp_remote_get( $url );
		
			wp_redirect( admin_url( '/edit.php?post_type=landingpage' ) );
			exit;
		}
	}
	
	$status = get_option( 'fca_lpc_activation_status' );
	if ( empty($status) && isset($_GET['post']) && get_post_type( $_GET['post'] ) === 'landingpage' || empty($status) && isset($_GET['post_type']) && $_GET['post_type'] === 'landingpage' ) {
        wp_redirect( admin_url( '/admin.php?page=landing-page-cat-splash' ) );
		exit;
    }

}
add_action('admin_init', 'fca_lpc_admin_redirects');

