<?php 
include_once( FCA_LPC_PLUGIN_DIR . '/includes/providers/localwp/optins.php' );

function fca_lpc_localwp_settings( $meta ) {
	global $post;
	
	$html = "<tr class='fca_lpc_localwp_setting_row'>";

		$html .= "<th>" . __('View Optins', 'landing-page-cat') . "</th>";
		
		$html .= "<td>";
			$html .= fca_lpc_info_span( __('Click here to view your optins.', 'landing-page-cat'), admin_url( "admin.php?page=landing-page-optins&id=$post->ID") );
		$html .= "</td>";

	$html .= "</tr>";
	
	return $html;
	
}

function fca_lpc_localwp_subscribe( $post_id, $email, $name = '' ) {
	//WRITE IN THE DB
	$data = array (
		'email' =>  $email,
		'name' => $name,
		'time' => current_time('mysql'),
		'ip' => $_SERVER['REMOTE_ADDR'],
	);

	return is_int ( add_post_meta( $post_id, 'fca_lpc_optin', $data ) );
	
}

function fca_lpc_get_optins( $post_id ) {
	//WRITE IN THE DB
	return get_post_meta( $post_id, 'fca_lpc_optin' );
	
}