<?php
	
////////////////////////////
// EDITOR PAGE 
////////////////////////////

include_once( FCA_LPC_PLUGIN_DIR . '/includes/providers/mailchimp.php' );
include_once( FCA_LPC_PLUGIN_DIR . '/includes/providers/zapier.php' );
include_once( FCA_LPC_PLUGIN_DIR . '/includes/providers/getresponse.php' );
include_once( FCA_LPC_PLUGIN_DIR . '/includes/providers/aweber.php' );
include_once( FCA_LPC_PLUGIN_DIR . '/includes/providers/activecampaign.php' );
include_once( FCA_LPC_PLUGIN_DIR . '/includes/providers/drip.php' );
include_once( FCA_LPC_PLUGIN_DIR . '/includes/providers/madmimi.php' );
include_once( FCA_LPC_PLUGIN_DIR . '/includes/providers/convertkit.php' );
include_once( FCA_LPC_PLUGIN_DIR . '/includes/providers/campaignmonitor.php' );
include_once( FCA_LPC_PLUGIN_DIR . '/includes/providers/localwp.php' );

//ENQUEUE ANY SCRIPTS OR CSS FOR OUR ADMIN PAGE EDITOR
function fca_lpc_admin_cpt_script( $hook ) {
	global $post;  
	if ( ($hook == 'post-new.php' || $hook == 'post.php')  &&  $post->post_type === 'landingpage' ) {  
		wp_enqueue_media();	
		wp_enqueue_style('dashicons');
		wp_enqueue_script('jquery');
		
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		
		wp_enqueue_style('fca_lpc_tooltipster_stylesheet', FCA_LPC_PLUGINS_URL . '/includes/tooltipster/tooltipster.bundle.min.css', array(), FCA_LPC_PLUGIN_VER );
		wp_enqueue_style('fca_lpc_tooltipster_borderless_css', FCA_LPC_PLUGINS_URL . '/includes/tooltipster/tooltipster-borderless.min.css', array(), FCA_LPC_PLUGIN_VER );
		wp_enqueue_style('fca_lpc_tooltipster_fca_css', FCA_LPC_PLUGINS_URL . '/includes/tooltipster/tooltipster-fca-theme.min.css', array(), FCA_LPC_PLUGIN_VER );
		wp_enqueue_script('fca_lpc_tooltipster_js', FCA_LPC_PLUGINS_URL . '/includes/tooltipster/tooltipster.bundle.min.js', array('jquery'), FCA_LPC_PLUGIN_VER, true );
		
		wp_enqueue_style('fca_lpc_select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css', array(), FCA_LPC_PLUGIN_VER );
		wp_enqueue_script('fca_lpc_select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array(), FCA_LPC_PLUGIN_VER, true );

		wp_enqueue_style('fca_lpc_codemirror', FCA_LPC_PLUGINS_URL . '/includes/codemirror/codemirror.min.css', array(), FCA_LPC_PLUGIN_VER );
		wp_enqueue_script('fca_lpc_codemirror', FCA_LPC_PLUGINS_URL . '/includes/codemirror/codemirror.min.js', array(), FCA_LPC_PLUGIN_VER, true );
		wp_enqueue_script('fca_lpc_codemirror_placeholder', FCA_LPC_PLUGINS_URL . '/includes/codemirror/cm-placeholder.min.js', array( 'fca_lpc_codemirror' ), FCA_LPC_PLUGIN_VER, true );
		wp_enqueue_script('fca_lpc_codemirror_css', FCA_LPC_PLUGINS_URL . '/includes/codemirror/codemirror-css.min.js', array( 'fca_lpc_codemirror' ), FCA_LPC_PLUGIN_VER, true );
		
		wp_enqueue_script('fca_lpc_editor_js', FCA_LPC_PLUGINS_URL . '/includes/editor/editor.js', array( 'fca_lpc_codemirror', 'fca_lpc_select2', 'jquery', 'fca_lpc_tooltipster_js', 'wp-color-picker' ), FCA_LPC_PLUGIN_VER, true );		
		wp_enqueue_style('fca_lpc_editor_stylesheet', FCA_LPC_PLUGINS_URL . '/includes/editor/editor.min.css', array(), FCA_LPC_PLUGIN_VER );
		
		$admin_data = array (
			'ajaxurl' => admin_url ( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'fca_lpc_admin_nonce' ),
			'post_id' => $post->ID,
		);
		
		wp_localize_script( 'fca_lpc_editor_js', 'fcaLpcData', $admin_data );
	}

}
add_action( 'admin_enqueue_scripts', 'fca_lpc_admin_cpt_script', 10, 1 );  

//ADD META BOXES TO EDIT CPT PAGE
function fca_lpc_add_custom_meta_boxes( $post ) {

	add_meta_box( 
		'fca_lpc_landing_page_content_meta_box',
		__( 'Layout & Content', 'landing-page-cat' ),
		'fca_lpc_render_content_meta_box',
		null,
		'normal',
		'high'
	);
	
	add_meta_box( 
		'fca_lpc_landing_page_settings_meta_box',
		__( 'Email Provider Settings', 'landing-page-cat' ),
		'fca_lpc_render_settings_meta_box',
		null,
		'normal',
		'high'
	);	
	
	add_meta_box( 
		'fca_lpc_landing_page_settings_text_meta_box',
		__( 'Email Optin Settings', 'landing-page-cat' ),
		'fca_lpc_render_settings_text_meta_box',
		null,
		'normal',
		'high'
	);	
	
	add_meta_box( 
		'fca_lpc_landing_page_deploy_meta_box',
		__( 'Setup', 'landing-page-cat' ),
		'fca_lpc_render_deploy_meta_box',
		null,
		'normal',
		'high'
	);	
	
	add_meta_box( 
		'fca_lpc_custom_css_meta_box',
		__( 'Custom CSS', 'landing-page-cat' ),
		'fca_lpc_render_custom_css_meta_box',
		null,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_landingpage', 'fca_lpc_add_custom_meta_boxes', 10 );

function fca_lpc_admin_nav() {
	global $post;
	if ( $post->post_type === 'landingpage'	) {
		$html = '<div id="lpc-nav">';
			
			$html .= '<h1 class="nav-tab-wrapper">';
				$html .= '<a href="#" id="nav-tab-deploy" data-target="#fca_lpc_landing_page_deploy_meta_box" class="nav-tab">' . __('Setup', 'landing-page-cat') . '</a>';
				$html .= '<a href="#" id="nav-tab-content" data-target="#fca_lpc_landing_page_content_meta_box" class="nav-tab nav-tab-active">' . __('Layout & Content', 'landing-page-cat') . '</a>';
				$html .= '<a href="#" id="nav-tab-optin" data-target="#fca_lpc_landing_page_settings_meta_box, #fca_lpc_landing_page_settings_text_meta_box, #fca_lpc_google_analytics_meta_box, #fca_lpc_custom_css_meta_box" class="nav-tab">' . __('Configuration', 'landing-page-cat') . '</a>';
			$html .= '</h1>';
			
		$html .= '</div>';
		echo $html;
	}
}
add_action( 'edit_form_after_title', 'fca_lpc_admin_nav' );	

function fca_lpc_render_custom_css_meta_box( $post ) {
	
	$meta = get_post_meta ( $post->ID, 'fca_lpc', true );
	$meta = empty ( $meta ) ? array() : $meta;
	$custom_css = empty( $meta['custom_css'] ) ? '' : $meta['custom_css'];
	
	$html = "<table class='fca-lpc-setting-table'>";
		$html .= '<tr>';
				$html .= '<td colspan=2>' . fca_lpc_input( 'custom_css', __( 'Add custom css here..', 'landing-page-cat' ), $custom_css, 'textarea', 'id="fca-pc-codemirror"' ) . '</td>';
		$html .=  '</tr>';
	$html .= '</table>';
	
	echo $html;
}

function fca_lpc_image_upload_input( $meta ) {
	
	$value = empty ( $meta['custom_background'] ) ? '' : $meta['custom_background'];
	$selected_bg = empty ( $meta['background_image'] ) ? '' : $meta['background_image'];
	$selected_class = $value === $selected_bg && $selected_bg !== '' ? 'fca-bg-selected' : '';
	
	$html = "<div id='fca-lpc-bg-upload' class='fca-lpc-image-upload fca-lpc-bg-item $selected_class'>";
		$html .= "<input type='hidden' class='fca-lpc-input-image fca-lpc-custom_background' name='fca_lpc[custom_background]' value='$value'>";

		$html .= "<button type='button' class='button-secondary fca-lpc-image-upload-btn'>" . __('Add Image', 'landing-page-cat') . "</button>";
			$html .= "<img class='fca-lpc-custom-image' src='$value'>";
			$html .= "<div class='fca-lpc-image-hover-controls'>";
				$html .= "<button type='button' class='button-secondary fca-lpc-image-change-btn'>" . __('Change', 'landing-page-cat') . "</button>";
				$html .= "<button type='button' class='button-secondary fca-lpc-image-revert-btn'>" . __('Remove', 'landing-page-cat') . "</button>";
			$html .=  '</div>';
	$html .= '</div>';
	
	echo $html;
}

function fca_lpc_render_content_meta_box( $post ) {
	
	$meta = get_post_meta ( $post->ID, 'fca_lpc', true );
	
	$meta = empty( $meta ) ? array() : $meta;

	$settings = array(
		'background_image',
		'bg_color',
		'headline_copy',
		'headline_color',
		'subheadline_copy',
		'subheadline_color',
		'show_name',
		'name_placeholder',
		'button_copy',
		'footer_copy',
		'button_copy_color',
		'footer_copy_color',
		'email_placeholder',
		'button_color',
		'button_border_color',
		'button_hover_color',
		'button_url',
		'button_mode',
		'button_post',		
		'show_skip',
		'skip_copy',
		'skip_color',
	);
	
	//ESCAPE INPUTS / MAKE SURE ARRAY IS SET
	forEach ($settings as $s) {
		$meta[$s] = !isSet( $meta[$s] ) ? '' : $meta[$s];
	}
	//BACKWARD COMPATIBILITY
	$meta['button_copy_color'] = empty( $meta['button_copy_color'] ) ? '#ffffff' : $meta['button_copy_color'];	
	$meta['footer_copy_color'] = empty( $meta['footer_copy_color'] ) ? '#ffffff' : $meta['footer_copy_color'];
	
	//FIX EXTRA BR EACH TIME IT SAVES
	$meta['subheadline_copy'] = str_replace( '<br />', '', $meta['subheadline_copy'] );
	
	//DEFAULTS
	$screen = get_current_screen();
	if ( $screen->action === 'add' ) {
		$meta['background_image'] = FCA_LPC_PLUGINS_URL . '/assets/cup-of-coffee-1920.jpg';
		$meta['bg_color'] = '#333';
		$meta['headline_copy'] = __('Get higher conversion rates on landing pages', 'landing-page-cat');
		$meta['headline_color'] = '#FFFFFF';
		$meta['subheadline_copy'] = '<p class="wysiwyg-text-align-center">' . __('Fascinating, proven insights from the world of landing pages and conversion optimization', 'landing-page-cat') . '</p>';
		$meta['subheadline_color'] = '#FFFFFF';
		$meta['show_name'] = '';
		$meta['name_placeholder'] = __('Your name', 'landing-page-cat');
		$meta['email_placeholder'] = __('Your Email', 'landing-page-cat');
		$meta['button_copy'] = __('Subscribe', 'landing-page-cat');
		$meta['button_copy_color'] = '#ffffff';
		$meta['footer_copy'] = '<p class="wysiwyg-text-align-center">&nbsp;</p>';
		$meta['footer_copy_color'] = '#ffffff';
		$meta['button_color'] = '#ff9933';
		$meta['button_border_color'] = '#d35400';
		$meta['button_hover_color'] = '#d35400';
		$meta['button_mode'] = 'page';
		$meta['button_post'] = 'home';
		$meta['button_url'] = 'https://example.com';
		$meta['skip_copy'] = __('No Thanks', 'landing-page-cat');
		$meta['skip_color'] = '#ffffff';
	}
	
	ob_start(); ?>
	<input type='hidden' name='fca_lpc_preview_url' id='fca_lpc_preview_url' value='<?php echo get_site_url() . '?landingpage=' . $post->ID ?>'>
			
	<table class='fca-lpc-setting-table'>
		<tr>
			<th class="fca-lpc-subheading fca-lpc-first-subheading"><?php _e('Background', 'landing-page-cat') ?></th>
		</tr>
		<?php fca_lpc_image_transparency_input( $meta ) ?>
		<tr>
			<th><?php _e( 'Choose a Background', 'landing-page-cat') ?></th>
			<td><?php echo fca_lpc_bg_image_select( $meta ) ?></td>
		</tr>
		<tr>
			<th class="fca-lpc-subheading"><?php _e( 'Particle Effects', 'landing-page-cat' ) ?></th>
		</tr>
		<tr>
			<th colspan=2 ><?php _e( 'Add beautiful particle animations to your landing page.', 'landing-page-cat' ) ?> 
			<a href='https://fatcatapps.com/lpdemo' target='_blank' ><?php _e( 'Click here for a demo', 'landing-page-cat' ) ?></a>.
			</th>
		</tr>
		<?php if ( function_exists( 'fca_lpc_particles_input' ) ){
			fca_lpc_particles_input( $meta );
		} else { ?>
			<tr>
				<th><?php _e( 'Enable', 'landing-page-cat' ) ?>
					<?php echo fca_lpc_tooltip( __('Premium Only', 'landing-page-cat') ) ?>
				</th>
				<td style='cursor: default;'><?php echo fca_lpc_input( 'particles_enabled', '', false, 'checkbox', 'disabled' ) ?></td>
			</tr>
		<?php } ?>
					
		<tr>
			<th class="fca-lpc-subheading"><?php _e( 'Headline', 'landing-page-cat') ?></th>
		</tr>		
		<tr>
			<th><?php _e( 'Copy', 'landing-page-cat') ?></th>
			<td>
				<?php echo fca_lpc_input( 'headline_copy', '', $meta['headline_copy'], 'text' ) ?>
				<?php echo fca_lpc_info_span( __('Supports HTML', 'landing-page-cat') ) ?>
			</td>
		</tr>		
		<tr>
			<th><?php _e( 'Color', 'landing-page-cat') ?></th>
			<td><?php echo fca_lpc_input( 'headline_color', '', $meta['headline_color'], 'color' ) ?></td>
		</tr>		
		<tr>
			<th class="fca-lpc-subheading"><?php _e( 'Description', 'landing-page-cat') ?></th>
		</tr>		
		<tr>
			<th><?php _e( 'Copy', 'landing-page-cat') ?></th>
			<td>
				<?php echo fca_lpc_input( 'subheadline_copy', '', $meta['subheadline_copy'], 'editor' ) ?>
			</td>
		</tr>		
		<tr>
			<th><?php _e( 'Color', 'landing-page-cat') ?></th>
			<td><?php echo fca_lpc_input( 'subheadline_color', '', $meta['subheadline_color'], 'color' ) ?></td>
		</tr>		
		<tr class="fca-lpc-settings-optin">
			<th class="fca-lpc-subheading"><?php _e( 'Email Optin', 'landing-page-cat') ?></th>
		</tr>				
		<tr class="fca-lpc-settings-optin">
			<th class="fca-lpc-subheading2"><?php _e( 'Name Field', 'landing-page-cat') ?></th>
		</tr>		
		<tr class="fca-lpc-settings-optin">
			<th><?php _e( 'Show?', 'landing-page-cat') ?></th>
			<td><?php echo fca_lpc_input( 'show_name', '', $meta['show_name'], 'checkbox' ) ?></td>
		</tr>		
		<tr id="fca-lpc-name-placeholder" class="fca-lpc-settings-optin">
			<th><?php _e( 'Placeholder Copy', 'landing-page-cat') ?></th>
			<td><?php echo fca_lpc_input( 'name_placeholder', '', $meta['name_placeholder'], 'text' ) ?></td>
		</tr>
		<tr class="fca-lpc-settings-optin">
			<th class="fca-lpc-subheading2"><?php _e( 'Email Field', 'landing-page-cat') ?></th>
		</tr>		
		<tr class="fca-lpc-settings-optin">
			<th><?php _e( 'Placeholder Copy', 'landing-page-cat') ?></th>
			<td><?php echo fca_lpc_input( 'email_placeholder', '', $meta['email_placeholder'], 'text' ) ?></td>
		</tr>		
		<tr class="fca-lpc-settings-optin fca-lpc-settings-button">
			<th class="fca-lpc-subheading"><?php _e( 'Button', 'landing-page-cat') ?></th>
		</tr>		
		<tr class="fca-lpc-settings-optin fca-lpc-settings-button">
			<th><?php _e( 'Copy', 'landing-page-cat') ?></th>
			<td>
				<?php echo fca_lpc_input( 'button_copy', '', $meta['button_copy'], 'text' ) ?>
			</td>
		</tr>		
		<tr class="fca-lpc-settings-optin fca-lpc-settings-button">
			<th><?php _e( 'Copy Color', 'landing-page-cat') ?></th>
			<td>
				<?php echo fca_lpc_input( 'button_copy_color', '', $meta['button_copy_color'], 'color' ) ?>
			</td>
		</tr>				
		<tr class="fca-lpc-settings-button">
			<th><?php _e( 'Redirect to', 'landing-page-cat') ?>
				<select style='width: auto margin-left: 6px' id='fca_lpc[button_mode]' name='fca_lpc[button_mode]' class='fca_lpc_select fca_lpc-button_mode'>
				<?php 
					$redirect_modes = array(
						'page' => __('Page', 'landing-page-cat'),
						'url' => __('URL', 'landing-page-cat'),
					);
					
					forEach ( $redirect_modes as $key => $value ) {
						if ( $key === $meta['button_mode'] ) {
							echo "<option value='$key' selected='selected'>$value</option>";
						} else {
							echo "<option value='$key'>$value</option>";
						}
					}
					
				?>
				</select>
			</th>
			<td>
				<?php echo fca_lpc_input( 'button_url', '', $meta['button_url'], 'url' ) ?>
				<div>
					<select style='width: 100%' name='fca_lpc[button_post]' id='fca_lpc_button_page_list_input'  class='fca_lpc_select fca_lpc_select2'>
						<?php 
						$values = array(
							'home' => __( 'Home Page', 'landing-page-cat' ),
							'blog' => __( 'Blog Page', 'landing-page-cat' ),
							'original' => __( 'Original Page (Welcome Gate)', 'landing-page-cat' ),
						);
						
						forEach ( $values as $key => $value ) {
							if ( $key === $meta['button_post'] ) {
								echo $key === 'original' ? "<option class='fca_lpc_original_post_option' value='$key' selected='selected'>$value</option>" : "<option value='$key' selected='selected'>$value</option>";
							} else {
								echo $key === 'original' ? "<option class='fca_lpc_original_post_option' value='$key'>$value</option>" : "<option value='$key'>$value</option>";
							}
						}
												
						$pages = get_pages();
						forEach ( $pages as $p ) {
							if ( $p->ID == $meta['button_post'] ) {
								echo "<option value='$p->ID' selected='selected'>" . __('Page', 'landing-page-cat') . " $p->ID - " . get_the_title( $p->ID ) . "</option>";
							} else {
								echo "<option value='$p->ID'>" . __('Page', 'landing-page-cat') . " $p->ID - " . get_the_title( $p->ID ) . "</option>";
							}
						}
						$posts = get_posts( array('posts_per_page' => -1 ) );
						forEach ( $posts as $p ) {
							if ( $p->ID == $meta['button_post'] ) {
								echo "<option value='$p->ID' selected='selected'>" . __('Post', 'landing-page-cat') . " $p->ID - " . get_the_title( $p->ID ) . "</option>";
							} else {
								echo "<option value='$p->ID'>" . __('Post', 'landing-page-cat') . " $p->ID - " . get_the_title( $p->ID ) . "</option>";
							}
						}
						?>
					</select>
				</div>
			</td>
		</tr>				
		<tr class="fca-lpc-settings-optin fca-lpc-settings-button">
			<th><?php _e( 'Button Color', 'landing-page-cat') ?></th>
			<td><?php echo fca_lpc_input( 'button_color', '', $meta['button_color'], 'color' ) ?></td>
		</tr>		
		<tr class="fca-lpc-settings-optin fca-lpc-settings-button">
			<th><?php _e( 'Bottom Border Color', 'landing-page-cat') ?></th>
			<td><?php echo fca_lpc_input( 'button_border_color', '', $meta['button_border_color'], 'color' ) ?></td>
		</tr>		
		<tr class="fca-lpc-settings-optin fca-lpc-settings-button">
			<th><?php _e( 'Hover Color', 'landing-page-cat') ?></th>
			<td><?php echo fca_lpc_input( 'button_hover_color', '', $meta['button_hover_color'], 'color' ) ?></td>
		</tr>
		<?php do_action( 'fca_lpc_button_after', $meta ) ?>
		<tr>
			<th class="fca-lpc-subheading">
				<?php _e( 'After Button Text', 'landing-page-cat') . 
					fca_lpc_tooltip( __('Use this area to display a link to your Privacy Policy or other important information', 'landing-page-cat') ) ?>
				</th>
		</tr>
		<tr>
			<th><?php _e( 'Copy', 'landing-page-cat') ?></th>
			<td class="fca_lpc_footer_copy_wysi">
				<?php echo fca_lpc_input( 'footer_copy', '', $meta['footer_copy'], 'editor' ) ?>
			</td>
		</tr>
		<tr>
			<th><?php _e( 'Color', 'landing-page-cat') ?></th>
			<td>
				<?php echo fca_lpc_input( 'footer_copy_color', '', $meta['footer_copy_color'], 'color' ) ?>
			</td>
		</tr>
	</table>
	<?php 
	echo ob_get_clean();
}

//BACKGROUND IMAGE SELECT
function fca_lpc_bg_image_select( $meta ) {
	
	$selected = $meta['background_image'];
	$color = $meta['bg_color'];
	$solid_color_selected = $selected === '' ? 'fca-bg-selected' : '';
	$images = array_slice( scandir( FCA_LPC_PLUGIN_DIR . '/assets/'), 2);
	//array map...
	forEach ( $images as $key => $value ) {
		$images[$key] = FCA_LPC_PLUGINS_URL . '/assets/' . $value;
	}
	
	ob_start();
	?>	
	<div class='fca-lpc-bg-select'>
		<input type='hidden' name='fca_lpc[background_image]' id='fca-lpc-bg' value='<?php echo $selected ?>'>
		
		<?php fca_lpc_image_upload_input( $meta ) ?>
		
		<div id='fca-lpc-bg-none' class='fca-lpc-bg-item <?php echo $solid_color_selected ?>' style='background-color: #fff;'>
			<?php echo __('Solid Color', 'landing-page-cat') . fca_lpc_input( 'bg_color', '', $color, 'color' ) ?>
		</div><br>
		<?php forEach ( $images as $img ) {
			$selected_class = $img === $selected ? 'fca-bg-selected' : '';
			echo "<div class='fca-lpc-bg-item $selected_class'><img src='$img'></div>";
		} ?>
		
		<p id="fca_lpc_more_image_link">
			<em><?php _e("Want more images? ", 'landing-page-cat') ?> <a href="https://fatcatapps.com/landingpagecat/images" target=_"blank"><?php _e("Click here", 'landing-page-cat') ?></a></em>
		</p>
	</div>
	<?php
	
	return ob_get_clean();
}

function fca_lpc_image_transparency_input( $meta ) {
	
	$bg_alpha = !isSet( $meta['bg_alpha'] ) ? 0.60 : $meta['bg_alpha'];
	
	$html = '<tr id="fca-lpc-custom-image-opacity-row" >';
		$html .= '<th>' . __('Opacity', 'landing-page-cat') . '</th>';
		$html .= "<td><input name='fca_lpc[bg_alpha]' id='fca-lpc-image-opacity-slider' type='range' max='1' min='0' step='0.01' value='$bg_alpha'></td>";
	$html .=  '</tr>';
	
	echo $html;
}

function fca_lpc_render_settings_text_meta_box ( $post ) {
	$meta = get_post_meta ( $post->ID, 'fca_lpc', true );
	$meta = empty ( $meta ) ? array() : $meta;
	//DEFAULTS
	$screen = get_current_screen();
	if ( $screen->action === 'add' ) {
		$meta['subscribe_message'] = __( 'Subscribing...', 'landing-page-cat' );
		$meta['thanks_message'] = __( 'Thank you! Please check your inbox for your confirmation email', 'landing-page-cat' );
		$meta['required_message'] = __( 'Please fill out this field to continue', 'landing-page-cat' );
		$meta['invalid_message'] = __( 'Please enter a valid email address. For example, "example@example.com"', 'landing-page-cat' );
	}
	
	$settings = array (
		'subscribe_message',
		'thanks_message',
		'required_message',
		'invalid_message',
	);
	
	//ESCAPE INPUTS / MAKE SURE ARRAY IS SET
	forEach ($settings as $s) {
		$meta[$s] = !isSet( $meta[$s] ) ? '' : $meta[$s];
	}
	
	$html = "<table class='fca-lpc-setting-table'>";
	
		$html .= '<tr>';
			$html .= '<th class="fca-lpc-subheading2 fca-lpc-first-subheading">' . __('Subscribing Message', 'landing-page-cat') . fca_lpc_tooltip( __('Enter the message a user will see while waiting for the subscription process to finish.', 'landing-page-cat') ) . '</th>';
		$html .=  '</tr>';
		
		$html .= '<tr>';
			$html .= '<th>' . __('Message', 'landing-page-cat') . '</th>';
			$html .= '<td>' . fca_lpc_input( 'subscribe_message', '', $meta['subscribe_message'], 'text') . '</td>';
		$html .=  '</tr>';

		$html .= '<tr>';
			$html .= '<th class="fca-lpc-subheading2">' . __('Thank You Message', 'landing-page-cat') . fca_lpc_tooltip( __('Enter the message a user will see after a successful optin.', 'landing-page-cat') ) . '</th>';
		$html .=  '</tr>';

		
		if ( function_exists('fca_lpc_render_thank_you_redirect_rows') ) {
			$html .= fca_lpc_render_thank_you_redirect_rows( $meta );
		} else {
			$html .= '<tr id="fca_lpc_thank_you_msg_row">';
				$html .= '<th>' . __('Thank You Message', 'landing-page-cat') . '</th>';
				$html .= '<td>' . fca_lpc_input( 'thanks_message', '', $meta['thanks_message'], 'text') . '</td>';
			$html .=  '</tr>';
		}
			
		$html .= '<tr>';
			$html .= '<th class="fca-lpc-subheading2">' . __('Error Messages', 'landing-page-cat') . fca_lpc_tooltip( __('Enter the messages a user will see if they do not enter valid information', 'landing-page-cat') ) . '</th>';
		$html .=  '</tr>';
		
		$html .= '<tr>';
			$html .= '<th>' . __('Field Required', 'landing-page-cat') . '</th>';
			$html .= '<td>' . fca_lpc_input( 'required_message', '', $meta['required_message'], 'text') . '</td>';
		$html .=  '</tr>';
		
		$html .= '<tr>';
			$html .= '<th>' . __('Invalid Email', 'landing-page-cat') . '</th>';
			$html .= '<td>' . fca_lpc_input( 'invalid_message', '', $meta['invalid_message'], 'text') . '</td>';
		$html .=  '</tr>';	
		
	$html .= "</table>";

	echo $html;	
}

function fca_lpc_render_settings_meta_box ( $post ) {
	
	$meta = get_post_meta ( $post->ID, 'fca_lpc', true );

	$providers = array (
		'activecampaign' => 'ActiveCampaign',
		'aweber' => 'Aweber',
		'campaignmonitor' => 'Campaign Monitor',
		'convertkit' => 'ConvertKit',
		'drip' => 'Drip',
		'getresponse' => 'GetResponse',
		'madmimi' => 'Mad Mimi',
		'mailchimp' => 'MailChimp',
		'zapier' => 'Zapier',
		'localwp' => 'Local WordPress (Store Optins Locally)',
	);
	
	$selected_provider = empty( $meta['provider'] ) ? 'localwp' : $meta['provider'];
	
	$html = "<table class='fca-lpc-setting-table'>";
		$html .= "<tr>";
			$html .= "<th>". __('Provider', 'landing-page-cat') . "</th>";
			$html .= "<td>" . fca_lpc_select( 'provider', $selected_provider, $providers ) . "</td>";
		$html .= "</tr>";
		
		$html .= fca_lpc_mailchimp_settings( $meta );
		$html .= fca_lpc_madmimi_settings( $meta );
		$html .= fca_lpc_zapier_settings( $meta );
		$html .= fca_lpc_getresponse_settings( $meta );
		$html .= fca_lpc_drip_settings( $meta );
		$html .= fca_lpc_aweber_settings( $meta );
		$html .= fca_lpc_activecampaign_settings( $meta );
		$html .= fca_lpc_campaignmonitor_settings( $meta );
		$html .= fca_lpc_convertkit_settings( $meta );
		$html .= fca_lpc_localwp_settings( $meta );
		
	$html .= "</table>";
	
	echo $html;
}


function fca_lpc_render_deploy_meta_box ( $post ) {
	
	$meta = get_post_meta ( $post->ID, 'fca_lpc', true );
	$meta = empty ( $meta ) ? array() : $meta;
	
	//DEFAULTS
	$screen = get_current_screen();
	if ( $screen->action === 'add' ) {
		$meta['deploy_mode'] = 'homepage';
		$meta['call_to_action'] = 'optin';
		$meta['welcome_exclude_search'] = 'on';
	}
	
	$settings = array(
		'deploy_mode',
		'deploy_url_url',
		'call_to_action',
		'welcome_exclude_search',
	);
	
	//MAKE SURE ARRAY IS SET
	forEach ($settings as $s) {
		$meta[$s] = !isSet( $meta[$s] ) ? '' : $meta[$s];
	}
	
	$html = "<table class='fca-lpc-setting-table'>";
		
		$html .= '<tr>';
			$html .= '<th>' . __('Landing Page Behavior', 'landing-page-cat') . fca_lpc_tooltip( fca_lpc_landing_behavior_tooltip() ) . '</th>';
			$html .= '<td>';
			
				$modes = array(
					'homepage' => __( 'Replace my homepage', 'landing-page-cat' ),
					'url' => __( 'Publish on a specific URL', 'landing-page-cat' ),
					'four_o_four' => __( 'Replace my 404 pages', 'landing-page-cat' ),
					'welcome' => __( 'Welcome Gate', 'landing-page-cat' ),
					'disabled' => __( 'Disable', 'landing-page-cat' ),
				);
				$html .= "<select id='fca_lpc_deploy_mode_input' name='fca_lpc[deploy_mode]' class='fca_lpc_select'>";
					forEach ( $modes as $key => $value ) {
						if ( $key === $meta['deploy_mode'] ) {
							$html .= "<option value='$key' selected='selected'>$value</option>";
						} else {
							if ( !function_exists( 'fca_lpc_render_welcome_gate_settings' ) && $key === 'welcome' ) {
								$html .= "<option disabled>$value " . __('(Premium Only)', 'landing-page-cat') . "</option>";
							} else if( !function_exists( 'fca_lpc_404_check' ) && $key === 'four_o_four' ) {
								$html .= "<option disabled>$value " . __('(Premium Only)', 'landing-page-cat') . "</option>";
							} else {
								$html .= "<option value='$key'>$value</option>";
							}
						}
					}
				
				$html .= "</select>";

			$html.= '</td>';
		$html .=  '</tr>';
			
		$html .= '<tr id="fca-lpc-redirect-url-input">';
			$html .= '<th></th>';
			$html .= '<td>' .  get_site_url() . '/' . "<input style='display:inline-block; width:auto; min-width:200px' type='text' class='fca-lpc-deploy_url_url' name='fca_lpc[deploy_url_url]' value='" . $meta['deploy_url_url'] . "'></td>";
		$html .=  '</tr>';

		if ( function_exists( 'fca_lpc_render_welcome_gate_settings' ) ) {
			$html .= fca_lpc_render_welcome_gate_settings( $meta );
		}
		
		$html .= '<tr>';
			$html .= '<th>' . __('Call to Action', 'landing-page-cat') . '</th>';
			$html .= '<td>';
			
				$options = array(
					'optin' => __( 'Email Optin (collect email subscribers)', 'landing-page-cat' ),
					'button' => __( 'Button (drive traffic to another URL)', 'landing-page-cat' ),
					'none' => __( 'None (no call to action displayed)', 'landing-page-cat' ),
				);
				
				$html .= "<select id='fca_lpc_cta_input' name='fca_lpc[call_to_action]' class='fca_lpc_select'>";
					forEach ( $options as $key => $value ) {
						if ( $key === $meta['call_to_action'] ) {
							$html .= "<option value='$key' selected='selected'>$value</option>";
						} else {
							$html .= "<option value='$key'>$value</option>";
						}
					}
				
				$html .= "</select>";

			$html .= '</td>';
		$html .=  '</tr>';
		
	$html .= '</table>';	
	
	echo $html;

}

function fca_lpc_landing_behavior_tooltip() {
	return __('Choose one of the following behaviors:', 'landing-page-cat') . '<br>' . 
		"<span style='font-weight: bold;'>" . __('Replace my homepage', 'landing-page-cat') . ' ... </span>' . 
		__('replaces the content of your homepage with this landing page.', 'landing-page-cat') . '<br>' . 
		"<span style='font-weight: bold;'>" . __('Publish on a specific URL', 'landing-page-cat') . ' ... </span>' . 
		__('publishes your landing page on a specific URL', 'landing-page-cat') . '<br>' . 
		"<span style='font-weight: bold;'>" . __('Replace my 404 pages', 'landing-page-cat') . ' ... </span>' . 
		__('replaces your 404 pages with this landing page.', 'landing-page-cat') . '<br>' . 
		"<span style='font-weight: bold;'>" . __('Welcome Gate', 'landing-page-cat') . ' ... </span>' . 
		__("show this landing page to visitors before they're able to view the rest of the site.", 'landing-page-cat'); 
}

//CUSTOM SAVE HOOK
function fca_lpc_save_post ( $post_id ) {
	
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return $post_id;
	}
	
	//ONLY DO OUR STUFF IF ITS A REAL SAVE, NOT A NEW IMPORTED ONE
	if ( array_key_exists( 'fca_lpc_preview_url', $_POST ) ) {
		update_post_meta( $post_id, 'fca_lpc', fca_lpc_sanitize_post_save( $_POST ) );
		wp_publish_post( $post_id );
	}	
}
add_action( 'save_post_landingpage', 'fca_lpc_save_post' );

function fca_lpc_sanitize_post_save( $post ) {
	
	//BG
	$data['custom_background'] = empty( $_POST['fca_lpc']['custom_background'] ) ? '' : esc_url( $_POST['fca_lpc']['custom_background'] );
	$data['background_image'] = empty( $post['fca_lpc']['background_image'] ) ? '' : esc_url( $post['fca_lpc']['background_image'] );
	$data['bg_color'] = empty( $post['fca_lpc']['bg_color'] ) ? '' : sanitize_text_field( $post['fca_lpc']['bg_color'] );
	$data['headline_copy'] = empty( $post['fca_lpc']['headline_copy'] ) ? '' : fca_lpc_kses( $post['fca_lpc']['headline_copy'] );
	$data['headline_color'] = empty( $post['fca_lpc']['headline_color'] ) ? '' : sanitize_text_field( $post['fca_lpc']['headline_color'] );
	$data['subheadline_copy'] = empty( $post['fca_lpc']['subheadline_copy'] ) ? '' : fca_lpc_kses(  $post['fca_lpc']['subheadline_copy'] );
	$data['subheadline_color'] = empty( $post['fca_lpc']['subheadline_color'] ) ? '' : sanitize_text_field( $post['fca_lpc']['subheadline_color'] );
	$data['show_name'] = empty( $post['fca_lpc']['show_name'] ) ? '' : sanitize_text_field( $post['fca_lpc']['show_name'] );
	$data['name_placeholder'] = empty( $post['fca_lpc']['name_placeholder'] ) ? '' : sanitize_text_field( $post['fca_lpc']['name_placeholder'] );
	$data['email_placeholder'] = empty( $post['fca_lpc']['email_placeholder'] ) ? '' : sanitize_text_field( $post['fca_lpc']['email_placeholder'] );
	$data['button_copy'] = empty( $post['fca_lpc']['button_copy'] ) ? '' : sanitize_text_field( $post['fca_lpc']['button_copy'] );
	$data['button_copy_color'] = empty( $post['fca_lpc']['button_copy_color'] ) ? '' : sanitize_text_field( $post['fca_lpc']['button_copy_color'] );
	$data['footer_copy'] = empty( $post['fca_lpc']['footer_copy'] ) ? '' : fca_lpc_kses( $post['fca_lpc']['footer_copy'] );
	$data['footer_copy_color'] = empty( $post['fca_lpc']['footer_copy_color'] ) ? '' : sanitize_text_field( $post['fca_lpc']['footer_copy_color'] );
	$data['button_color'] = empty( $post['fca_lpc']['button_color'] ) ? '' : sanitize_text_field( $post['fca_lpc']['button_color'] );
	$data['button_border_color'] = empty( $post['fca_lpc']['button_border_color'] ) ? '' : sanitize_text_field( $post['fca_lpc']['button_border_color'] );
	$data['button_hover_color'] = empty( $post['fca_lpc']['button_hover_color'] ) ? '' : sanitize_text_field( $post['fca_lpc']['button_hover_color'] );
	$data['button_url'] = empty( $post['fca_lpc']['button_url'] ) ? '' : esc_url( $post['fca_lpc']['button_url'] );
	$data['button_post'] = empty( $post['fca_lpc']['button_post'] ) ? '' : sanitize_text_field( $post['fca_lpc']['button_post'] );
	$data['button_mode'] = empty( $post['fca_lpc']['button_mode'] ) ? 'page' : sanitize_text_field( $post['fca_lpc']['button_mode'] );
	$data['bg_alpha'] = !isSet( $post['fca_lpc']['bg_alpha'] ) ? 0.60 : floatVal( $post['fca_lpc']['bg_alpha'] );
	$data['call_to_action'] = empty( $post['fca_lpc']['call_to_action'] ) ? '' : sanitize_text_field( $post['fca_lpc']['call_to_action'] );
	$data['event_tracking'] = empty( $post['fca_lpc']['event_tracking'] ) ? '' : 'on';
	$data['show_skip'] = empty( $post['fca_lpc']['show_skip'] ) ? '' : 'on';
	$data['skip_copy'] = empty( $post['fca_lpc']['skip_copy'] ) ? 'No Thanks' : sanitize_text_field( $post['fca_lpc']['skip_copy'] );
	$data['skip_color'] = empty( $post['fca_lpc']['skip_color'] ) ? '#ffffff' : sanitize_text_field( $post['fca_lpc']['skip_color'] );
	$data['success_redirect'] = empty( $post['fca_lpc']['success_redirect'] ) ? '' : sanitize_text_field( $post['fca_lpc']['success_redirect'] );
	$data['success_url'] = empty( $post['fca_lpc']['success_url'] ) ? '' : sanitize_text_field( $post['fca_lpc']['success_url'] );
	$data['success_post'] = empty( $post['fca_lpc']['success_post'] ) ? '' : sanitize_text_field( $post['fca_lpc']['success_post'] );
	$data['success_mode'] = empty( $post['fca_lpc']['success_mode'] ) ? 'page' : sanitize_text_field( $post['fca_lpc']['success_mode'] );
	
	//PUBLISH SETTINGS
	$data['deploy_mode'] = empty( $post['fca_lpc']['deploy_mode'] ) ? '' : sanitize_text_field( $post['fca_lpc']['deploy_mode'] );
	$data['deploy_url_url'] = empty( $post['fca_lpc']['deploy_url_url'] ) ? '' : sanitize_text_field( $post['fca_lpc']['deploy_url_url'] );
	
	//TEXT SETTINGS
	$data['invalid_message'] = empty( $post['fca_lpc']['invalid_message'] ) ? '' : sanitize_text_field( $post['fca_lpc']['invalid_message'] );
	$data['required_message'] = empty( $post['fca_lpc']['required_message'] ) ? '' : sanitize_text_field( $post['fca_lpc']['required_message'] );
	$data['thanks_message'] = empty( $post['fca_lpc']['thanks_message'] ) ? '' : sanitize_text_field( $post['fca_lpc']['thanks_message'] );
	$data['subscribe_message'] = empty( $post['fca_lpc']['subscribe_message'] ) ? '' : sanitize_text_field( $post['fca_lpc']['subscribe_message'] );
	
	//CUSTOM CSS
	$data['custom_css'] = empty( $post['fca_lpc']['custom_css'] ) ? '' : fca_lpc_kses( $post['fca_lpc']['custom_css'] );
	
	//PROVIDER SETTINGS
	$data['provider'] = empty( $post['fca_lpc']['provider'] ) ? '' : sanitize_text_field( $post['fca_lpc']['provider'] );
	
	//MAILCHIMP
	$data['mailchimp_key'] = empty( $post['fca_lpc']['mailchimp_key'] ) ? '' : sanitize_text_field( $post['fca_lpc']['mailchimp_key'] );
	$data['mailchimp_single_optin'] = empty( $post['fca_lpc']['mailchimp_single_optin'] ) ? '' : 'on';
	$data['mailchimp_list'] = empty( $post['fca_lpc']['mailchimp_list'] ) ? '' : sanitize_text_field( $post['fca_lpc']['mailchimp_list'] );
	
	//MADMIMI
	$data['madmimi_key'] = empty( $post['fca_lpc']['madmimi_key'] ) ? '' : sanitize_text_field( $post['fca_lpc']['madmimi_key'] );
	$data['madmimi_email'] = empty( $post['fca_lpc']['madmimi_email'] ) ? '' : sanitize_text_field( $post['fca_lpc']['madmimi_email'] );
	$data['madmimi_list'] = empty( $post['fca_lpc']['madmimi_list'] ) ? '' : sanitize_text_field( $post['fca_lpc']['madmimi_list'] );
	
	//CONVERTKIT
	$data['convertkit_key'] = empty( $post['fca_lpc']['convertkit_key'] ) ? '' : sanitize_text_field( $post['fca_lpc']['convertkit_key'] );
	$data['convertkit_list'] = empty( $post['fca_lpc']['convertkit_list'] ) ? '' : sanitize_text_field( $post['fca_lpc']['convertkit_list'] );
	
	//ZAPIER
	$data['zapier_url'] = empty( $post['fca_lpc']['zapier_url'] ) ? '' : esc_url ( $post['fca_lpc']['zapier_url'] );
	
	//GETRESPONSE
	$data['getresponse_key'] = empty( $post['fca_lpc']['getresponse_key'] ) ? '' : sanitize_text_field( $post['fca_lpc']['getresponse_key'] );
	$data['getresponse_list'] = empty( $post['fca_lpc']['getresponse_list'] ) ? '' : sanitize_text_field( $post['fca_lpc']['getresponse_list'] );
	
	//CAMPAIGNMONITOR
	$data['campaignmonitor_key'] = empty( $post['fca_lpc']['campaignmonitor_key'] ) ? '' : sanitize_text_field( $post['fca_lpc']['campaignmonitor_key'] );
	$data['campaignmonitor_list'] = empty( $post['fca_lpc']['campaignmonitor_list'] ) ? '' : sanitize_text_field( $post['fca_lpc']['campaignmonitor_list'] );
	$data['campaignmonitor_id'] = empty( $post['fca_lpc']['campaignmonitor_id'] ) ? '' : sanitize_text_field( $post['fca_lpc']['campaignmonitor_id'] );

	//AWEBER
	$data['aweber_key'] = empty( $post['fca_lpc']['aweber_key'] ) ? '' : sanitize_text_field( $post['fca_lpc']['aweber_key'] );
	$data['aweber_list'] = empty( $post['fca_lpc']['aweber_list'] ) ? '' : sanitize_text_field( $post['fca_lpc']['aweber_list'] );
		
	//DRIP
	$data['drip_id'] = empty( $post['fca_lpc']['drip_id'] ) ? '' : sanitize_text_field( $post['fca_lpc']['drip_id'] );
	$data['drip_key'] = empty( $post['fca_lpc']['drip_key'] ) ? '' : sanitize_text_field( $post['fca_lpc']['drip_key'] );
	$data['drip_list'] = empty( $post['fca_lpc']['drip_list'] ) ? '' : sanitize_text_field( $post['fca_lpc']['drip_list'] );
	
	//ACTIVE CAMPAIGN
	$data['activecampaign_url'] = empty( $post['fca_lpc']['activecampaign_url'] ) ? '' : esc_url( $post['fca_lpc']['activecampaign_url'] );
	$data['activecampaign_key'] = empty( $post['fca_lpc']['activecampaign_key'] ) ? '' : sanitize_text_field( $post['fca_lpc']['activecampaign_key'] );
	$data['activecampaign_list'] = empty( $post['fca_lpc']['activecampaign_list'] ) ? '' : sanitize_text_field( $post['fca_lpc']['activecampaign_list'] );
	
	
	return apply_filters( 'fca_lpc_sanitize_post_save', $data );

}

//Redirect when Save & Preview button is clicked
function fca_lpc_save_preview_redirect ( $location ) {
	global $post;
	if ( !empty( $_POST['fca_lpc_preview_url'] ) ) {
		// Flush rewrite rules
		global $wp_rewrite;
		$wp_rewrite->flush_rules( true );

		return esc_url( $_POST['fca_lpc_preview_url'] );
	}
 
	return $location;
}
add_filter('redirect_post_location', 'fca_lpc_save_preview_redirect');