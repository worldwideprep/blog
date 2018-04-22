<?php 

	global $post;
	if ( !empty ( $_GET['landingpage'] ) ) {
		$post = get_post( $_GET['landingpage'] );
	}
	
	$is_404 = is_404();
	
	if ( $is_404 ) {
		global $wp_query;
		$wp_query->is_404 = false;		
		status_header(200);
	}
	
	$meta = get_post_meta( $post->ID, 'fca_lpc', true );
	$meta = empty( $meta ) ? array() : $meta;
	
	$mode = empty ( $meta['deploy_mode'] ) ? 'disabled' : $meta['deploy_mode'];
	$title = get_the_title( $post->ID );
	$bg_alpha = !isSet( $meta['bg_alpha'] ) ? 0.6 : $meta['bg_alpha'];
	$bg_alpha = $meta['background_image'] === '' ? 0 : $meta['bg_alpha'];
	$bg_color = $meta['background_image'] !== '' ? 'rgb(45,45,45)' : $meta['bg_color'];
	$button_color = empty( $meta['button_copy_color'] ) ? '#ffffff' : $meta['button_copy_color'];
	
	$JSON = json_encode ( array(
		'mode' => $mode,
		'nonce' => wp_create_nonce( 'fca_lpc_landing_page_nonce' ),
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'subscribe_message' => $meta['subscribe_message'],
		'thanks_message' => $meta['thanks_message'],
		'required_message' => $meta['required_message'],
		'invalid_message' => $meta['invalid_message'],
		'post_id' => $post->ID,
		'redirect_on' => !empty( $meta['success_redirect'] ) && $meta['success_redirect'] === 'redirect' ? true : false,
		'redirect_url' => fca_lpc_redirect_url($meta, $post, 'success'),
		'page_title' => $title,
		'particles_enabled' => !empty( $meta['particles_enabled'] ),
		'event_tracking' => empty( $meta['event_tracking'] ) ? false : true,
		'do_cookie' => !empty( $meta['success_cookie'] ) && $mode === 'welcome' ? true : false,
	) );
	

	
?>

<!doctype html>
<html>
	<head>
		<title><?php echo $title ?></title>
		<?php wp_head(); ?>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<style type='text/css'>#fca-lpc-optin-button:hover{ background-color: <?php echo $meta['button_hover_color'] ?> !important}</style>
		<?php if ( !empty( $meta['custom_css'] ) ) {
			echo '<style type="text/css">' . $meta['custom_css'] . '</style>';
		} ?>
	</head>
	<body>
		<div id='fca-lpc-wrapper'  style='background-color: <?php echo $bg_color ?>; background-image: url("<?php echo $meta['background_image'] ?>"); background-image: linear-gradient(to bottom, rgba(45,45,45,<?php echo $bg_alpha?>) 0%,rgba(45,45,45,<?php echo $bg_alpha?>) 100%), url("<?php echo $meta['background_image'] ?>");'>
			<?php do_action('fca_lpc_template_body', $meta ); ?>
			<div id='fca-lpc-main'>
				<div id='fca-lpc-inner'>
					<input id='fca-lpc-data' type='hidden' value='<?php echo $JSON ?>'>
					<div id='fca-lpc-headline' style='color:<?php echo $meta['headline_color'] ?>'><?php echo $meta['headline_copy'] ?></div>
					<div id='fca-lpc-subheadline' style='color:<?php echo $meta['subheadline_color'] ?>'><?php echo do_shortcode( apply_filters( 'the_content', $meta['subheadline_copy'] ) ) ?></div>
					<?php if ( empty( $meta['call_to_action'] ) OR $meta['call_to_action'] === 'optin' ) { ?>
						<div id='fca-lpc-optin'>
							<?php if ( $meta['show_name'] ) { ?>
								<input name='name' title='<?php echo $meta['required_message'] ?>' type='text' class='fca-lpc-input-element' id='fca-lpc-name-input' placeholder='<?php echo $meta['name_placeholder'] ?>' />
							<?php } ?>
							<input name='email' title='<?php echo $meta['required_message'] ?>' type='email' class='fca-lpc-input-element' id='fca-lpc-email-input' placeholder='<?php echo $meta['email_placeholder'] ?>' />
							<button type='button' data-mode='optin' title='<?php echo $meta['thanks_message'] ?>' class='fca-lpc-input-element' id='fca-lpc-optin-button' style='color:<?php echo $button_color ?>; background-color:<?php echo $meta['button_color'] ?>; border-bottom-color:<?php echo $meta['button_border_color'] ?>'><?php echo $meta['button_copy'] ?></button>
						</div>
					<?php } else if ( $meta['call_to_action'] === 'button' ) {?>
						<button type='button' data-mode='button' onclick='window.open("<?php echo fca_lpc_redirect_url($meta, $post, 'button') ?>","_blank")' class='fca-lpc-input-element' id='fca-lpc-optin-button' style='color:<?php echo $button_color ?>; background-color:<?php echo $meta['button_color'] ?>; border-bottom-color:<?php echo $meta['button_border_color'] ?>'><?php echo $meta['button_copy'] ?></button>
					<?php }
						if ( !empty( $meta['footer_copy'] ) ) {	?>
							<div id='fca-lpc-after-button' style='color:<?php echo $meta['footer_copy_color'] ?>'><?php echo $meta['footer_copy'] ?></div>
						<?php }
						if ( !empty( $meta['show_skip'] ) && !$is_404 ) {	?>
							<a id='fca-lpc-skip-link' href='<?php echo add_query_arg( 'fca_lpc_skip', $post->ID ) ?>' style='color: <?php echo $meta['skip_color'] ?>;'><?php echo $meta['skip_copy'] ?></a>
					<?php } ?>	
				
				</div>
			</div>
		</div>
		<div id='fca-lpc-footer' style='display: none;'>
			<?php wp_footer(); ?> 
		</div>
	</body>
</html>