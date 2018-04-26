<?php
/*
* Plugin Name: Stylesheet Per Page
*
* Description: Add custom stylesheets, IE override stylesheets, iOS stylesheets, to any page or post on your Wordpress website.
*
* Author: Josh Kohlbach
* Author URI: http://www.codemyownroad.com
* Plugin URI: http://www.codemyownroad.com/products/stylesheet-per-page-wordpress-plugin/
* Version: 1.1.2
*/


/*******************************************************************************
** addCustomSheet()
**
** Echos a stylesheet to the header if it exists
**
** @since 0.2
*******************************************************************************/
function addCustomSheet($sheetName, $delimiter = '') {
	if (empty($sheetName))
		return;

	$possible_src_1 = trailingslashit(get_stylesheet_directory()) . 'css/' .
		$delimiter . $sheetName . '.css';

	$possible_src_2 = trailingslashit(get_stylesheet_directory()) .
		$delimiter . $sheetName . '.css';

	$use_src_1 = false;

	// Retrieve plugin options
	$stylesheetPerPage = get_option('stylesheetPerPage');

	// Optionally, check if the file exists or not
	if (isset($stylesheetPerPage['check_for_file']) && $stylesheetPerPage['check_for_file'] == 'on') {
		if (file_exists($possible_src_1)) {
			// file exists in /css directory, give preference to that sheet
			$use_src_1 = true;
		} else {
			// file doesn't exists in /css dir, check the root dir
			if (!file_exists($possible_src_2)) {
				// file doesn't exist in the root dir either
				return;
			}
		}
	} else {
		// need both files because we're just print it regardless of whether it
		// exists or not
		$src1 = trailingslashit(get_bloginfo('stylesheet_directory')) . 'css/' .
			$delimiter . $sheetName . '.css';
		$src2 = trailingslashit(get_bloginfo('stylesheet_directory')) .
			$delimiter . $sheetName . '.css';
	}

	if ($use_src_1) {
		// File exists in /css dir
		$src = trailingslashit(get_bloginfo('stylesheet_directory')) . 'css/' .
			$delimiter . $sheetName . '.css';
	} else {
		// Use file in root dir
		$src = trailingslashit(get_bloginfo('stylesheet_directory')) .
			$delimiter . $sheetName . '.css';
	}

	if (isset($stylesheetPerPage['check_for_file']) && $stylesheetPerPage['check_for_file'] == 'on') {
		echo '<link href="' . $src . '" rel="stylesheet" type="text/css" />';
	} else {
		echo '<link href="' . $src1 . '" rel="stylesheet" type="text/css" />';
		echo '<link href="' . $src2 . '" rel="stylesheet" type="text/css" />';
	}

}

/*******************************************************************************
** addCustomStylesheets()
**
** Echo a stylesheet or optionally an array of stylesheets back to the browser
**
** @since 0.1
*******************************************************************************/
function addCustomStylesheets($stylesheets) {
	if (!is_array($stylesheets)) {
		addCustomSheet($stylesheets, 'page-');
	} else {
		foreach ($stylesheets as $sheetName) {
			addCustomSheet($sheetName,(is_page() ? 'page-' : ''));
		}
	}
}

/*******************************************************************************
** addIEStylesheets()
**
** Allows you to define ie.css, ie8.css, ie7.css and ie6.css files in your theme
** directory (or theme directory plus /css) for isolating IE only CSS.
**
** @since 0.3
*******************************************************************************/
function addIEStylesheets() {
	$ieSheets = array(
		'IE' => 'ie',
		'lte IE 8' => 'ie8',
		'lte IE 7' => 'ie7',
		'lte IE 6' => 'ie6'
	);

	foreach ($ieSheets as $key => $value) {
		echo '<!--[if ' . $key . ']>';
		addCustomSheet($value);
		echo "<![endif]-->\n";
	}

}

/*******************************************************************************
** addIosStylesheet()
**
** Allows you to define a ios.css file in your theme
** directory (or theme directory plus /css) for isolating iOS devices (ipad,
** iphone, ipod).
**
** @since 0.5
*******************************************************************************/
function addIosStylesheet() {
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') ||
		strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') ||
		strpos($_SERVER['HTTP_USER_AGENT'], 'iPod')) {
		addCustomSheet('ios');
	}
}

/*******************************************************************************
** stylesheetPerPage()
**
** Echo a stylesheet for the current page
**
** @since 0.1
*******************************************************************************/
function stylesheetPerPage() {
	global $wp_query;
	$page_obj = $wp_query->get_queried_object();

	if (is_page()) {
		addCustomStylesheets(
			$page_obj->post_name
		);
	} else if (is_author()) {
		addCustomStylesheets(
			array(
				'user',
				'user-' . $page_obj->user_login
			)
		);
	} else if (is_singular()) {
		addCustomStylesheets(
			array(
				$page_obj->post_type,
				$page_obj->post_type . '-' . $page_obj->post_name
			)
		);
	} else if (is_category()) {
		addCustomStylesheets(
			array(
				'category',
				'category-' . $page_obj->slug
			)
		);
	} else if (is_tag()) {
		addCustomStylesheets(
			array(
				'tag',
				'tag-' . $page_obj->slug
			)
		);
	} else if (is_archive()) {
		addCustomStylesheets(
			array(
				'archive'
			)
		);
	} else if (is_home()) {
		addCustomStylesheets(
			array(
				'home'
			)
		);
	} else if (is_front_page()) {
		addCustomStylesheets(
			array(
				'front-page'
			)
		);
	}

	// 1.1.2: add user logged in stylesheets
	if (is_user_logged_in()) {
		addCustomStylesheets(
			array(
				'logged-in'
			)
		);
	} else if (!is_user_logged_in()) {
		addCustomStylesheets(
			array(
				'logged-out'
			)
		);
	}

	$stylesheetPerPage = get_option('stylesheetPerPage');

	if (!is_admin() && isset($stylesheetPerPage['add_ie_stylesheets']) && $stylesheetPerPage['add_ie_stylesheets'] == 'on'){
		addIEStylesheets();
	}

	if (!is_admin() && isset($stylesheetPerPage['add_ios_stylesheet']) &&  $stylesheetPerPage['add_ios_stylesheet'] == 'on'){
		addIosStylesheet();
	}
}

/*******************************************************************************
** stylesheetPerPageMenu()
**
** Setup the plugin options menu
**
** @since 0.2
*******************************************************************************/
function stylesheetPerPageMenu() {
	if (is_admin()) {
		register_setting('stylesheet-per-page', 'stylesheetPerPage');
		add_options_page('Stylesheet Per Page Settings', 'Stylesheet Per Page', 'administrator', __FILE__, 'stylesheetPerPageOptions', plugins_url('/images/icon.png', __FILE__));
	}
}

/*******************************************************************************
** stylesheetPerPageOptions()
**
** Present the options page
**
** @since 0.2
*******************************************************************************/
function stylesheetPerPageOptions() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have suffifient permissions to access this page.') );
	}

	echo '<div class="wrap">' . screen_icon() . '<h2>Stylesheet Per Page</h2>';

	$stylesheetPerPage = get_option('stylesheetPerPage');
	$stylesheetPerPage['check_for_file'] = isset($stylesheetPerPage['check_for_file']) ? 'checked="checked"' : '';
	$stylesheetPerPage['add_ie_stylesheets'] = isset($stylesheetPerPage['add_ie_stylesheets']) ? 'checked="checked"' : '';
	$stylesheetPerPage['add_ios_stylesheet'] = isset($stylesheetPerPage['add_ios_stylesheet']) ? 'checked="checked"' : '';

	echo '<form method="post" action="options.php">';

	wp_nonce_field('update-options');
	settings_fields( 'stylesheet-per-page' );

	echo '<table class="form-table">
	<tr valign="top">
	<th scope="row" style="white-space: nowrap;">Check for files before placing in header?</th>
	<td>
	<input type="checkbox" name="stylesheetPerPage[check_for_file]" id="check_for_file" ' .
	$stylesheetPerPage['check_for_file'] . ' />
	</td></tr>

	<tr valign="top">
	<th scope="row" style="white-space: nowrap;"><label for="stylesheetPerPage[add_ie_stylesheets]">Add IE specific stylesheets?</label></th>
	<td>
	<input type="checkbox" name="stylesheetPerPage[add_ie_stylesheets]" id="add_ie_stylesheets" ' .
	$stylesheetPerPage['add_ie_stylesheets'] . ' />
	<p><span class="description">Allows you to define ie.css, ie7.css and ie6.css files in your theme directory<br />
	for isolating IE only CSS.</span></p>
	</td></tr>

	<tr valign="top">
	<th scope="row" style="white-space: nowrap;"><label for="stylesheetPerPage[add_ios_stylesheet]">Add iOS stylesheet?</label></th>
	<td>
	<input type="checkbox" name="stylesheetPerPage[add_ios_stylesheet]" id="add_ios_stylesheet" ' .
	$stylesheetPerPage['add_ios_stylesheet'] . ' />
	<p><span class="description">Allows you to define ios.css in your theme directory to add a specific sheet for iOS devices (ipad, iphone, ipod)</span></p>
	</td></tr>

	</table>

	<input type="hidden" name="page_options" value="stylesheetPerPage" />

	<p class="submit">
	<input type="submit" class="button-primary" value="Save Changes" />
	</p>

	</form>
	</div>';

}


/*******************************************************************************
** initStylesheetPerPage()
**
** Initialize the stylesheet per page plugin
**
** @since 0.1
*******************************************************************************/
function initStylesheetPerPage() {
	add_filter('wp_head', 'stylesheetPerPage', 999);
	add_action('admin_menu', 'stylesheetPerPageMenu');
}

add_action('init', 'initStylesheetPerPage', 1);

?>
