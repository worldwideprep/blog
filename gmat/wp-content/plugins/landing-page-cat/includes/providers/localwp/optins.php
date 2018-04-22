<?php
	
////////////////////////////
// OPTINS PAGE 
////////////////////////////

//REGISTER OPTINS PAGE
function fca_lpc_optins_page() {
	add_submenu_page(
		null,
		__('Entries', 'landing-page-cat'),
		__('Entries', 'landing-page-cat'),
		'manage_options',
		'landing-page-optins',
		'fca_lpc_render_optins_page'
	);
}
add_action( 'admin_menu', 'fca_lpc_optins_page' );

function fca_lpc_optins_page_enqueue( $id ) {
	
	wp_enqueue_script('fca_lpc_optins_js', FCA_LPC_PLUGINS_URL . '/includes/providers/localwp/optins.js', array(), FCA_LPC_PLUGIN_VER, true );		
	wp_enqueue_style( 'fca_lpc_optins_stylesheet', FCA_LPC_PLUGINS_URL . '/includes/providers/localwp/optins.min.css', array(), FCA_LPC_PLUGIN_VER );
	
	$admin_data = array (
		
		'post_id' => $id,
		'ajaxurl' => admin_url ( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'fca_lpc_optins_nonce' ),
	);
		
	wp_localize_script( 'fca_lpc_optins_js', 'lpcAdminData', $admin_data );
	
}

function fca_lpc_render_optins_page() {
	
	$id = intval( $_GET['id'] );
	$title = get_the_title( $id );
	$title = empty( $title ) ? __('(no title)', 'landing-page-cat') : $title;
	$entries = fca_lpc_get_optins( $id );
	$entries = empty ( $entries ) ? array() : $entries;
	
	fca_lpc_optins_page_enqueue( $id );

	$html = '<div id="fca-lpc-optins-page" >';
			
		$html .= "<h1><a style='text-decoration: none;' title='" . __( 'Edit Landing Page', 'quiz-cat' ) . "'href='" . admin_url("post.php?post=$id&action=edit") . "'>$title</a></h1>";
		
		$html .= '<p><a class="button button-primary" id="lpc-csv-download" href="' . admin_url( "admin.php?page=landing-page-optins&fca_lpc_export=true&id=$id") . '" >' .  __( 'Download CSV', 'quiz-cat' ) . '</a></p>';

		$html .= '<div id="entries-tab" class="entries-page-tab">';	
			$html .=  fca_lpc_optins_table( $entries );
		$html .= '</div>';
		
	$html .= '</div>';
	
	echo $html;
	
}

function fca_lpc_export_optins() {
	if ( is_user_logged_in() && current_user_can('manage_options') && isset( $_GET['fca_lpc_export'] ) && isset( $_GET['id'] ) ) {
		$id = intval( $_GET['id'] );
		$entries = fca_lpc_get_optins( $id );
		$entries = empty ( $entries ) ? array() : $entries;
			
		header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename=landing-page-cat-optins-$id.csv");
		
		echo "name,email,time,ip\n";
		
		forEach ( $entries as $entry ) {
			echo $entry['name'] . ',' . $entry['email'] . ',' . $entry['time'] . ',' . $entry['ip'] . "\n";
		}
		die(); 
	}
}
add_action( 'plugins_loaded', 'fca_lpc_export_optins' );

function fca_lpc_optins_table( $entries ) {
	$html = "<table class='fca-lpc-optins-table fca-lpc-all-entries widefat striped'>";
		$html .= '<tr><th id="fca-lpc-optins-number">#</th>' .
			'<th id="fca-lpc-optins-name">' . __( 'Name' , 'conntest-cat') . '</th>' . 
			'<th id="fca-lpc-optins-email">' . __( 'Email' , 'conntest-cat') . '</th>' .
			'<th id="fca-lpc-optins-time">' . __( 'Time' , 'conntest-cat') . '</th>' . 
			'<th id="fca-lpc-optins-ip">' . __( 'IP' , 'conntest-cat') . '</th>' .
		'</tr>';
		
		$n = 1;
		forEach ( $entries as $key => $entry ) {
			
			$html .= "<tr><td>" . 
				$n	. '</td><td>' .
				$entry['name']	. '</td><td><input class="fca-lpc-email-input" type="text" readonly="readonly" onclick="this.select()" value="' . 
				$entry['email'] . '"/></td><td class="fca-lpc-status">' .
				$entry['time'] . '</td><td>' . 
				$entry['ip'] . '</td>' .
				'</tr>';
			
			$n++;
		}
		
	$html .= '</table>';
	
	return $html;
	
}
