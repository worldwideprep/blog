<?php
	
function fca_lpc_add_marketing_metaboxes( $post ) {

	add_meta_box( 
		'fca_lpc_marketing_metabox',
		__( 'Upgrade to Premium', 'landing-page-cat' ),
		'fca_lpc_render_marketing_metabox',
		null,
		'side',
		'default'
	);

}
add_action( 'add_meta_boxes_landingpage', 'fca_lpc_add_marketing_metaboxes', 11, 1 );

function fca_lpc_render_marketing_metabox( $post ) {
	

	wp_enqueue_script('fca_lpc_sidebar_js', FCA_LPC_PLUGINS_URL . '/includes/editor/sidebar.min.js', array( 'jquery' ), FCA_LPC_PLUGIN_VER, true );		
	$user = wp_get_current_user();

	ob_start(); ?>

	<ul style='padding-left: 30px; text-indent: -24px;'>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Particle Effects', 'landing-page-cat' ); ?></li>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Build Welcome Gates', 'landing-page-cat' ); ?></li>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Build 404 Pages', 'landing-page-cat' ); ?></li>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Google Analytics Integration', 'landing-page-cat' ); ?></li>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( '30+ New Curated Background Images', 'landing-page-cat' ); ?></li>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Redirect To "Thank You" Page', 'landing-page-cat' ); ?></li>
		<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Priority Support', 'landing-page-cat' ); ?></li>
	</ul>
	<hr>
	<div style="text-align: center; width: 100%;">
		<p><strong><?php _e('Get 20% Off Premium', 'landing-page-cat'); ?></strong></p>
		<p><?php _e("Enter your email and we'll send you a coupon for 20% off premium.", 'landing-page-cat'); ?></p>
		<input style="width: 100%; margin-bottom: 8px;" title='<?php _e('Please enter a valid e-mail address.', 'landing-page-cat')?>'  type='email' value='<?php echo $user->user_email ?>' id='fca_lpc_sidebar_email'>
		<button style="width: 100%;" type='button' class='button button-primary' id='fca_lpc_sidebar_submit'><?php _e('Send me the coupon', 'landing-page-cat'); ?></button>
		<p id='fca_lpc_sidebar_success' style='display:none;'><strong><?php _e('Thanks!  Please check your inbox to claim your discount.', 'landing-page-cat'); ?></strong></p>

	</div> 

	<?php 
		
	echo ob_get_clean();
}
