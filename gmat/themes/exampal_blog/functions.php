<?php
ini_set('log_errors', 'On');
ini_set('display_errors', 'Off');
ini_set('error_reporting', E_ALL);
if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', false);
}
if (!defined('WP_DEBUG_LOG')) {
    define('WP_DEBUG_LOG', true);
}
if (!defined('WP_DEBUG_DISPLAY')) {
    define('WP_DEBUG_DISPLAY', false);
}
ini_set('upload_max_size', '64M');
ini_set('post_max_size', '64M');
ini_set('max_execution_time', '300');
@ini_set('upload_max_size', '64M');
@ini_set('post_max_size', '64M');
@ini_set('max_execution_time', '300');

/* ========================================================================================================================

Required external files

======================================================================================================================== */

require_once('external/starkers-utilities.php');

/** Add Redux Framework & extras */
require get_template_directory() . '/admin/admin-init.php';

/* ========================================================================================================================

Theme specific settings

======================================================================================================================== */

add_theme_support('title-tag');

add_theme_support('post-thumbnails');


if (function_exists('add_image_size')) {
    add_image_size('event_thumb', 100, 100, true);

    add_image_size('home-banner', 960, 300, true);
    add_image_size('book-thumb', 200, 9999, false);
};

register_nav_menus(array('primary' => 'Primary Navigation'));
register_nav_menus(array('footer' => 'Footer Navigation'));
register_nav_menus(array('site-info' => 'Lower footer navigation'));
register_nav_menus(array('site-info2' => 'Even Lower footer navigation'));


add_filter('relevanssi_match', 'custom_field_weights');

function custom_field_weights($match)
{
    $featured = get_post_meta($match->doc, 'author', true);
    if ('1' == $featured) {
        $match->weight = $match->weight * 2;
    } else {
        $match->weight = $match->weight / 2;
    }
    return $match;
}


add_filter('wpseo_metabox_prio', function () {
    return 'low';
});

add_action('init', 'jk_remove_wc_breadcrumbs');
function jk_remove_wc_breadcrumbs()
{
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
}


/*PUT THIS IN YOUR CHILD THEME FUNCTIONS FILE*/

/*STEP 1 - REMOVE ADD TO CART BUTTON ON PRODUCT ARCHIVE (SHOP) */

function remove_loop_button()
{
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
}

add_action('init', 'remove_loop_button');


function sv_wc_customer_order_csv_export_customer_headers($column_headers)
{
    return array_merge(array('username' => 'username', 'userid' => 'userid', 'payment' => 'payment',), $column_headers);
}

add_filter('wc_customer_order_csv_export_customer_headers', 'sv_wc_customer_order_csv_export_customer_headers');

function sv_wc_customer_order_csv_export_customer_row($customer_data, $user)
{
    $username = '';
    $payment = '';
    $user_id = '';


    if (0 !== $user->ID) {
        $username = $user->user_login;
        $payment = $user->method_of_payment;
        $user_id = $user->ID;

    }
    return array_merge(array('username' => $username, 'payment' => $payment, 'userid' => $user_id), $customer_data);
}

add_filter('wc_customer_order_csv_export_customer_row', 'sv_wc_customer_order_csv_export_customer_row', 10, 2);


add_filter('woocommerce_add_to_cart_redirect', 'rv_redirect_on_add_to_cart');
function rv_redirect_on_add_to_cart()
{

    //Get product ID
    if (isset($_POST['add-to-cart'])) {

        $product_id = (int)apply_filters('woocommerce_add_to_cart_product_id', $_POST['add-to-cart']);

        //Check if product ID is in the proper taxonomy and return the URL to the redirect product
        if (has_term('membership', 'product_cat', $product_id))
            return get_permalink(5);

    }

}


function check_product_in_cart()
{
//Check to see if user has product in cart
    global $woocommerce;

//assigns a default negative value
//  categories targeted 17, 18, 19

    $lundi_in_cart = false;

// start of the loop that fetches the cart items

    foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {
        $_product = $values['data'];
        $terms = get_the_terms($_product->id, 'product_cat');

// second level loop search, in case some items have several categories
        foreach ($terms as $term) {
            $_categoryid = $term->term_id;
        }

        if (($_categoryid === 16)) {
            //category is in cart!
            $product_in_cart = true;
        }

    }

    return $product_in_cart;
}

/**
 * Remove product tabs
 *
 */
function woo_remove_product_tab($tabs)
{
    unset($tabs['description']);                      // Remove the description tab
    unset($tabs['reviews']);                                  // Remove the reviews tab
    unset($tabs['additional_information']);   // Remove the additional information tab
    return $tabs;
}

add_filter('woocommerce_product_tabs', 'woo_remove_product_tab', 98);


// Add a custom user role

$result = add_role('member', __('Member'),

    array(

        'read' => true, // true allows this capability
        'edit_posts' => false, // Allows user to edit their own posts
        'edit_pages' => false, // Allows user to edit pages
        'edit_others_posts' => false, // Allows user to edit others posts not just their own
        'create_posts' => false, // Allows user to create new posts
        'manage_categories' => false, // Allows user to manage post categories
        'publish_posts' => false, // Allows the user to publish, otherwise posts stays in draft mode
        'edit_themes' => false, // false denies this capability. User can’t edit your theme
        'install_plugins' => false, // User cant add new plugins
        'update_plugin' => false, // User can’t update any plugins
        'update_core' => false // user cant perform core updates

    )

);

$instresult = add_role('institution', __('Institution'),

    array(

        'read' => true, // true allows this capability
        'edit_posts' => false, // Allows user to edit their own posts
        'edit_pages' => false, // Allows user to edit pages
        'edit_others_posts' => false, // Allows user to edit others posts not just their own
        'create_posts' => false, // Allows user to create new posts
        'manage_categories' => false, // Allows user to manage post categories
        'publish_posts' => false, // Allows the user to publish, otherwise posts stays in draft mode
        'edit_themes' => false, // false denies this capability. User can’t edit your theme
        'install_plugins' => false, // User cant add new plugins
        'update_plugin' => false, // User can’t update any plugins
        'update_core' => false // user cant perform core updates

    )

);

$preresult = add_role('memberreg', __('Member Registration'),

    array(

        'read' => true, // true allows this capability
        'edit_posts' => false, // Allows user to edit their own posts
        'edit_pages' => false, // Allows user to edit pages
        'edit_others_posts' => false, // Allows user to edit others posts not just their own
        'create_posts' => false, // Allows user to create new posts
        'manage_categories' => false, // Allows user to manage post categories
        'publish_posts' => false, // Allows the user to publish, otherwise posts stays in draft mode
        'edit_themes' => false, // false denies this capability. User can’t edit your theme
        'install_plugins' => false, // User cant add new plugins
        'update_plugin' => false, // User can’t update any plugins
        'update_core' => false // user cant perform core updates

    )

);


/* remove the unnecessary roles */
remove_role('subscriber');
remove_role('editor');
remove_role('author');
remove_role('contributor');


function wpbeginner_numeric_posts_nav()
{

    if (is_singular())
        return;

    global $wp_query;

    /** Stop execution if there's only 1 page */
    if ($wp_query->max_num_pages <= 1)
        return;

    $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
    $max = intval($wp_query->max_num_pages);

    /**    Add current page to the array */
    if ($paged >= 1)
        $links[] = $paged;

    /**    Add the pages around the current page to the array */
    if ($paged >= 3) {
        $links[] = $paged - 1;
        $links[] = $paged - 2;
    }

    if (($paged + 2) <= $max) {
        $links[] = $paged + 2;
        $links[] = $paged + 1;
    }

    echo '<div class="navigation"><ul>' . "\n";

    /**    Previous Post Link */
    if (get_previous_posts_link())
        printf('<li>%s</li>' . "\n", get_previous_posts_link());

    /**    Link to first page, plus ellipses if necessary */
    if (!in_array(1, $links)) {
        $class = 1 == $paged ? ' class="active"' : '';

        printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link(1)), '1');

        if (!in_array(2, $links))
            echo '<li>…</li>';
    }

    /**    Link to current page, plus 2 pages in either direction if necessary */
    sort($links);
    foreach ((array)$links as $link) {
        $class = $paged == $link ? ' class="active"' : '';
        printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($link)), $link);
    }

    /**    Link to last page, plus ellipses if necessary */
    if (!in_array($max, $links)) {
        if (!in_array($max - 1, $links))
            echo '<li>…</li>' . "\n";

        $class = $paged == $max ? ' class="active"' : '';
        printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($max)), $max);
    }

    /**    Next Post Link */
    if (get_next_posts_link())
        printf('<li>%s</li>' . "\n", get_next_posts_link());

    echo '</ul></div>' . "\n";

}


/* ========================================================================================================================

Actions and Filters

======================================================================================================================== */

add_action('wp_enqueue_scripts', 'starkers_script_enqueuer');

add_filter('body_class', array('Starkers_Utilities', 'add_slug_to_body_class'));


/* ========================================================================================================================

Custom Post Types - include custom post types and taxonimies here e.g.

======================================================================================================================== */

require_once('external/custom-post-types.php');


add_action('widgets_init', 'theme_slug_widgets_init');
function theme_slug_widgets_init()
{
    register_sidebar(array(
        'name' => __('Home Sidebar', 'theme-slug'),
        'id' => 'home-sidebar',
        'description' => __('Widgets in this area will be shown on the home page.', 'theme-slug'),
        'before_widget' => '<div id="%1$s" class="widget row %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Blog Home Sidebar', 'theme-slug'),
        'id' => 'blog-home-sidebar',
        'description' => __('Widgets in this area will be shown on the blog home page.', 'theme-slug'),
        'before_widget' => '<div id="%1$s" class="widget row %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>',
    ));
    register_sidebar(array(
        'name' => __('Blog Home Sidebar Top', 'theme-slug'),
        'id' => 'blog-home-sidebar-top',
        'description' => __('Widgets in this area will be shown on the blog home page.', 'theme-slug'),
        'before_widget' => '<div id="%1$s" class="widget row %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Login widget', 'theme-slug'),
        'id' => 'login-widget',
        'description' => __('Widgets for the login area.', 'theme-slug'),
        'before_widget' => '<div id="%1$s" class="widget row %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<a class="widgettitle" href="#">',
        'after_title' => '</a>',
    ));
}

/**
 * WooCommerce Extra Feature
 * --------------------------
 *
 * Change number of related products on product page
 * Set your own value for 'posts_per_page'
 *
 */
function woo_related_products_limit()
{
    global $product;

    $args['posts_per_page'] = 6;
    return $args;
}

add_filter('woocommerce_output_related_products_args', 'jk_related_products_args');
function jk_related_products_args($args)
{
    $args['posts_per_page'] = 4; // 4 related products
    $args['columns'] = 2; // arranged in 2 columns
    return $args;
}


/* ========================================================================================================================

Scripts

======================================================================================================================== */

function starkers_script_enqueuer()
{
    //wp_register_script( 'bxslider', get_template_directory_uri().'/js/jquery.bxslider.min.js', array( 'jquery' ) );
    //wp_enqueue_script( 'bxslider' );
    wp_register_script('jscook', get_template_directory_uri() . '/js/js.cookie.js', array('jquery'));
    wp_enqueue_script('jscook');

    wp_register_script('site', get_template_directory_uri() . '/js/site.js', array('jquery'));
    wp_enqueue_script('site');
    wp_register_script('wayp', get_template_directory_uri() . '/js/wayp/waypoint.js', array('jquery'));
    wp_enqueue_script('wayp');
    wp_register_script('wayp-sticky', get_template_directory_uri() . '/js/wayp/shortcuts/sticky.js', array('jquery'));
    //wp_enqueue_script( 'wayp-sticky' );
    wp_register_script('wayp-inf', get_template_directory_uri() . '/js/wayp/shortcuts/infinite.js', array('jquery'));

    // Intercom
    wp_register_script('exampal-intercom', get_template_directory_uri() . '/js/intercom.js', array('jquery'), '', true);
    wp_enqueue_script('exampal-intercom');

    // Google tag manager
    wp_register_script('exampal-gtm', get_template_directory_uri() . '/js/gtm-events.js', array('jquery'));
    wp_enqueue_script('exampal-gtm');

    //wp_enqueue_script( 'wayp-inf' );
    wp_localize_script(
        'site',
        'ajax_script',
        array('ajaxurl' => admin_url('admin-ajax.php'),
            'ofs_ajax_nonce_sign_up' => wp_create_nonce('ofs_ajax_nonce_sign_up'),
        ));

    wp_register_style('bxslider-style', get_stylesheet_directory_uri() . '/jquery.bxslider.css', '', '', 'screen');
    wp_enqueue_style('bxslider-style');

    wp_register_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', '', '', 'screen');
    wp_enqueue_style('font-awesome');

    wp_register_style('google-font', '//fonts.googleapis.com/css?family=Lato:400,700', '', '', 'screen');
    wp_enqueue_style('google-font');
    /*
            wp_register_style( 'screen', get_stylesheet_directory_uri().'/style.css', '', '', 'screen' );
            wp_enqueue_style( 'screen' ); */
}

/* ========================================================================================================================

Comments

======================================================================================================================== */

/**
 * Custom callback for outputting comments
 *
 * @return void
 * @author Keir Whitaker
 */
function starkers_comment($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment;
    ?>
    <?php if ($comment->comment_approved == '1'): ?>
    <li>
    <article id="comment-<?php comment_ID() ?>">
        <?php echo get_avatar($comment); ?>
        <h4><?php comment_author_link() ?></h4>
        <time><a href="#comment-<?php comment_ID() ?>" pubdate><?php comment_date() ?> at <?php comment_time() ?></a>
        </time>
        <?php comment_text() ?>
    </article>
<?php endif;
}


function filter_plugin_updates($value)
{
    if (isset($value->response['advanced-custom-fields-pro/acf.php'])) unset($value->response['advanced-custom-fields-pro/acf.php']);
    if (isset($value->response['contextual-related-posts/contextual-related-posts.php'])) unset($value->response['contextual-related-posts/contextual-related-posts.php']);
    if (isset($value->response['mailchimp-for-wp/mailchimp-for-wp.php'])) unset($value->response['mailchimp-for-wp/mailchimp-for-wp.php']);
    if (isset($value->response['popup-maker/popup-maker.php'])) unset($value->response['popup-maker/popup-maker.php']);
    if (isset($value->response['zm-ajax-login-register/plugin.php'])) unset($value->response['zm-ajax-login-register/plugin.php']);

    return $value;
}

add_filter('site_transient_update_plugins', 'filter_plugin_updates');


function get_excerpt_by_id($post_id, $length = 100, $read_more_small = '')
{
    $the_post = get_post($post_id); //Gets post ID
    $the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
    $excerpt_length = $length; //Sets excerpt length by word count
    $the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
    $words = explode(' ', $the_excerpt, $excerpt_length + 1);

    if (count($words) > $excerpt_length) :
        array_pop($words);
        array_push($words, '[...]');
        $the_excerpt = implode(' ', $words);
    endif;

    $read_more = '';
    if ($read_more_small != '') $read_more = '<a class="read_more_small" href="' . esc_url(get_the_permalink()) . '" title="Permalink to ' . get_the_title() . '" rel="bookmark">' . __('Read More', 'exampal_blog') . '</a>';
    else $read_more = '';
    $the_excerpt = '<p>' . $the_excerpt . $read_more . '</p>';

    return $the_excerpt;
}


$extra_fields = array(
    array('posit', __('Position', 'rc_cucm'), true),
);

// Use the user_contactmethods to add new fields
add_filter('user_contactmethods', 'rc_add_user_contactmethods');

// Add our fields to the registration process
add_action('register_form', 'rc_register_form_display_extra_fields');
add_action('user_register', 'rc_user_register_save_extra_fields', 100);

/**
 * Add custom users custom contact methods
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
function rc_add_user_contactmethods($user_contactmethods)
{

    // Get fields
    global $extra_fields;

    // Display each fields
    foreach ($extra_fields as $field) {
        if (!isset($contactmethods[$field[0]]))
            $user_contactmethods[$field[0]] = $field[1];
    }

    // Returns the contact methods
    return $user_contactmethods;
}


/**
 * Show custom fields on registration page
 *
 * Show custom fields on registration if field third parameter is set to true
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
function rc_register_form_display_extra_fields()
{

    // Get fields
    global $extra_fields;

    // Display each field if 3th parameter set to "true"
    foreach ($extra_fields as $field) {
        if ($field[2] == true) {
            if (isset($_POST[$field[0]])) {
                $field_value = $_POST[$field[0]];
            } else {
                $field_value = '';
            }
            ?>
            <p>
                <label for="<?php echo $field[0]; ?>"><?php echo $field[1]; ?><br/>
                    <input type="text" name="<?php echo $field[0]; ?>" id="<?php echo $field[0]; ?>" class="input"
                           value="<?php echo $field_value; ?>" size="20"/></label>
                </label>
            </p>
            <?php
        } // endif
    } // end foreach
}

/**
 * Save field values
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
function rc_user_register_save_extra_fields($user_id, $password = '', $meta = array())
{

    // Get fields
    global $extra_fields;

    $userdata = array();
    $userdata['ID'] = $user_id;

    // Save each field
    foreach ($extra_fields as $field) {
        if ($field[2] == true) {
            if (!isset($userdata[$field[0]])) $userdata[$field[0]] = '';
            if (!isset($_POST[$field[0]])) $_POST[$field[0]] = '';
            $userdata[$field[0]] = $_POST[$field[0]];
        } // endif
    } // end foreach

    $new_user_id = wp_update_user($userdata);
}


/*  AJAX loader for tabs  */
add_action('wp_ajax_nopriv_cbloader', 'cb_loader_code');
add_action('wp_ajax_cbloader', 'cb_loader_code');
function cb_loader_code()
{

    check_ajax_referer('security_token', 'security');
    $data = $_POST;

    unset($data['security'], $data['action']);
    if (isset($data['type'])) $type = $data['type'];
    if (isset($data['cat'])) $cat = $data['cat'];

    ob_start();
    $paged = esc_attr($_GET['paged']);
    if ($paged == '') $paged = '1';

    if ($type == 'recent') query_posts('posts_per_page=5&post_status=publish&post_type=post&paged=' . $paged);

    if ($type == 'must_read') {
        $ids = get_field('f8', '217');
        query_posts(array('post__in' => $ids, 'post_status' => 'publish', 'post_type' => 'post', 'posts_per_page' => '5', 'paged' => $paged));
    }
    if ($type == 'cat') {
        //	echo $cat;
        query_posts('cat=' . $cat . '&post_status=publish&posts_per_page=5&post_type=post&paged=' . $paged);
    }
    //	if($type=='recent')  query_posts('posts_per_page=5&post_status=publish&post_type=post&paged='.$paged);
    if (have_posts()): ?>

        <?php while (have_posts()) : the_post(); ?>
            <li class="infinite-item <?php echo $hidemeta = get_post_meta(get_the_ID(), 'hide_post_home', 'true'); ?>">

                <article>
                    <?php $bg_img = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'large');
                    $text = get_excerpt_by_id(get_the_ID(), '60', 'yes');
                    ?>
                    <?php if (has_post_thumbnail()) { ?>
                        <div class="col span_6"><a href="<?php the_permalink(); ?>" title="<?php the_title; ?>">
                            <img src="<?php echo $bg_img[0]; ?>" alt="<?php echo get_the_title(); ?>"/></a>
                        </div><?php } ?>
                    <?php if (has_post_thumbnail()){ ?>
                    <div class="content_right col span_6"><?php } ?>
                        <time datetime="<?php the_time('Y-m-d'); ?>"
                              pubdate><?php _e('Posted On ', 'exampal_blog'); ?><?php the_time('Y-m-d'); ?></time>
                        <a class="article_list_title" href="<?php esc_url(the_permalink()); ?>"
                           title="Permalink to <?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a>

                        <div class="content_excerpt"><?php echo $text; ?>

                        </div>
                        <?php if (has_post_thumbnail()){ ?></div><?php } ?>
                    <div class="cl"></div>
                </article>
            </li>
        <?php endwhile; ?>

        <a class="infinite-more-link" href="<?php $paged_n = $paged + 1;
        echo get_home_url() . '/?paged=' . $paged_n . '&type=' . $type . '&customcat=' . $cat; ?>"></a>
        <?php
    endif;
    $data = ob_get_contents();
    ob_end_clean();
    die($data);
}


add_action('wp_insert_comment', 'comment_inserted', 99, 2);

function comment_inserted($comment_id, $comment_object)
{
    $uid = get_current_user_id();
    if ($uid == '0') {
        //add new user to wp
        $email = $comment_object->comment_author_email;
        if (null == username_exists($email)) {

            // Generate the password and create the user
            $password = wp_generate_password(12, false);
            $user_id = wp_create_user($email, $password, $email);

            // Set the nickname
            wp_update_user(
                array(
                    'ID' => $user_id,
                    'nickname' => $email
                )
            );

            // Set the role
            $user = new WP_User($user_id);
            $user->set_role('member');
            update_user_meta($user_id, 'is_newsletter', '1');
            wp_new_user_notification($user_id, $password);

        } // end if
    }

}


add_action('wp_logout', create_function('', 'wp_redirect(home_url());exit();'));


add_action('add_meta_boxes', 'add_delivery_metaboxes');

function add_delivery_metaboxes()
{
    add_meta_box('delivery_meta', 'Background Video', 'delivery_meta', 'popup', 'side', 'default');
}

function delivery_meta()
{
    global $post;

    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
        wp_create_nonce(plugin_basename(__FILE__)) . '" />';

    // Get the location data if its already been entered
    $vid_url = get_post_meta($post->ID, 'vid_url', true);
    $vid_img = get_post_meta($post->ID, 'vid_img', true);

    echo '<label for="vid_url">Video URL</label><br/><input type="text" id="vid_url" value="' . $vid_url . '" name="vid_url"/><br/><br/>';
    echo '<label for="vid_img">Video Image URL</label><br/><input type="text" id="vid_img" value="' . $vid_img . '" name="vid_img"/>';

}


function save_delivery_meta($post_id, $post)
{

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if (!wp_verify_nonce($_POST['eventmeta_noncename'], plugin_basename(__FILE__))) {
        return $post->ID;
    }

    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID))
        return $post->ID;

    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.

    $events_meta['vid_url'] = $_POST['vid_url'];
    $events_meta['vid_img'] = $_POST['vid_img'];

    // Add values of $events_meta as custom fields

    foreach ($events_meta as $key => $value) { // Cycle through the $events_meta array!
        if ($post->post_type == 'revision') return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if (get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if (!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }

}

add_action('save_post', 'save_delivery_meta', 1, 2); // save the custom fields


if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title' => 'Site Settings',
        'menu_title' => 'Site Settings',
        'menu_slug' => 'site-settings',
        'capability' => 'edit_posts',
        'redirect' => false
    ));

}


function custom_menu_page_removing()
{
    remove_menu_page('ws-custom-login');
}

add_action('admin_menu', 'custom_menu_page_removing');


function ms_image_editor_default_to_gd($editors)
{
    $gd_editor = 'WP_Image_Editor_GD';

    $editors = array_diff($editors, array($gd_editor));
    array_unshift($editors, $gd_editor);

    return $editors;
}

add_filter('wp_image_editors', 'ms_image_editor_default_to_gd');


function news_letter($atts, $content = null)
{
    esc_attr(extract(shortcode_atts(array('s' => ''), $atts)));

    $cat_op = get_field('cat_op', 'option');
    $cat = get_the_category();
    $cat_id = $cat[0]->cat_ID;

    $c1 = 'Sign up to our blog';
    $c2 = 'Get weekly tips on your way to business school';
    $c3 = 'Your email...';
    $c4 = 'Sign up to our blog';
    $return_html = '';
    foreach ($cat_op as $cat_op_v) {
        if ($cat_op_v['category'] == $cat_id) {
            $c1 = $cat_op_v['f1'];
            $c2 = $cat_op_v['f2'];
            $c3 = $cat_op_v['f3'];
            $c4 = $cat_op_v['f4'];
            $return_html .= '<script type="text/javascript">
 		jQuery(document).ready(function(){
			jQuery(\'.single_nl .nl_form_top input[type="text"]\').attr("placeholder","' . $c3 . '");
			jQuery(\'.single_nl .nl_form_top input.submit\').val("' . $c4 . '");
		});
 		</script>';
        }
    }

    $return_html .= '<section class="newsletter_top nins single_nl"><div class="container">
<div class="single_sign_up nl_head">' . $c1 . '</div>
<div class="single_info nl_right">' . $c2 . '</div>
<div class="nl_form_top">' . do_shortcode('[mc4wp_form id="97"]') . '</div>
<div class="cl"></div></div>
</section>';

    return apply_filters('news_letter', $return_html);
}

add_shortcode('news_letter', 'news_letter');

function banner_728($atts, $content = null)
{
    esc_attr(extract(shortcode_atts(array('url' => '', 'image' => ''), $atts)));
    $return_html = '<a href="' . $url . '" target="_blank" class="banner_img inside_banner"><img src="' . $image . '" alt="banner"/></a>';
    return apply_filters('banner_728', $return_html);
}

add_shortcode('banner_728', 'banner_728');


/*
 * Fix Cancel Reply seo issu
 */
function exampal_fix_seo_cancel_reply_link($formatted_link, $link, $text)
{
    $style = isset($_GET['replytocom']) ? '' : ' style="display:none;"';
    $link = esc_html(get_permalink()) . '#respond';

    $formatted_link = '<a rel="nofollow" id="cancel-comment-reply-link" href="' . $link . '"' . $style . '>' . $text . '</a>';

    return $formatted_link;
}

add_filter('cancel_comment_reply_link', 'exampal_fix_seo_cancel_reply_link', 10, 3);


/************************************
 * **********************************
 * **********************************
 *        Add Sign up widget
 * **********************************
 * **********************************
 * **********************************/

// Register and load the widget
function wpb_sign_up_widget()
{
    register_widget('wpb_sign_up_widget');
}

add_action('widgets_init', 'wpb_sign_up_widget');

// Creating the widget 
class wpb_sign_up_widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(

// Base ID of your widget
            'wpb_sign_up_widget',

// Widget name will appear in UI
            __('Sign Up Widget', 'wpb_widget_exampal'),

// Widget description
            array('description' => __('Sign Up Widget. Home page.', 'wpb_widget_exampal'),)
        );
    }

// Creating widget front-end

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

        // This is where you run the code and display the output
        $view = '
              <div class="sign--up">
              
                <form class="sign--up-form" method="post" autocomplete="off">
                  <div class="sign--up-fields">
                    <input type="email" value="" name="sign-email" required="required" placeholder="Your Email Address..." autocomplete="off" readonly onfocus="this.removeAttribute(\'readonly\');">
                    <input type="password" value="" name="sign-password" required="required" placeholder="Password" autocomplete="off" readonly onfocus="this.removeAttribute(\'readonly\');">
                    <input type="tel" value="" name="sign-phone" placeholder="Phone number" maxlength="13" minlength="11">
                  </div>
                    <input type="submit" class="sign--up-submit submit" value="Start a free GMAT course" >   
                    <div class="sign--up-request"> <p>Please wait.. </p></div>
                    <div class="message"></div>
                </form>
              </div>';
        echo $view;
        echo $args['after_widget'];

    }

// Widget Backend 
    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title', 'wpb_widget_exampal');
        }
// Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>
        <?php
    }

// Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
} // Class wpb_widget ends here

/**
 * Registers new user
 * @return boolean
 */
function exampal_sign_up_form()
{
    check_ajax_referer('ofs_ajax_nonce_sign_up', 'security');
    if (isset($_POST) && ($_POST['action'] == 'exampal_sign_up_form')) {
        $email = trim(strip_tags($_POST['email']));
        $phone = trim(strip_tags($_POST['phone']));
        $pass = $_POST['pass'];
        $signature = "nZE1hbQWp7seBfcn5sMJMXDhsY4Pg";
        $APIToken = "1WXEWZFZJVCOKPeJjfvk1GDiWs1SAFpG8OdGjEt1gwcgoD23FTcnrhIooB53";

        $curlValues = array(
            'email' => $email,
            'password' => $pass,
            'signature' => $signature,
            'api_token' => $APIToken,
        );

        if (emailValidate($email)) {

            if (strlen($phone) != 0) {
                $curlValues['phone'] = $phone;
            }


            // Get cURL resource
            $curl = curl_init();
            // Set some options - we are passing in a useragent too here
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 0,
                CURLOPT_POST => 1,
                CURLOPT_URL => 'https://exampal.com/api/user/create/' . $APIToken . '/',
                CURLOPT_USERAGENT => 'Codular Sample cURL Request',
                CURLOPT_POSTFIELDS => $curlValues,
            ));
            // Send the request & save response to $resp
            $resp = curl_exec($curl);
            // Close request to clear up some resources
            curl_close($curl);


            die();

        }
    }

    return false;

}

add_action('wp_ajax_exampal_sign_up_form', 'exampal_sign_up_form');
add_action('wp_ajax_nopriv_exampal_sign_up_form', 'exampal_sign_up_form');

/**
 * Email validate
 * @param string $email
 * @return boolean
 */
function emailValidate($email)
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        return true;
    }
    return false;
}


function sign_up_form_func($atts)
{
    shortcode_atts(array(
        'title' => ''
    ), $atts);

    $title = (isset($atts['title'])) ? esc_html($atts['title']) : "Get a free GMAT course";


    $title = (isset($atts['title'])) ? esc_html($atts['title']) : "Get a free GMAT course";
    $description = (isset($atts['description'])) ? esc_html($atts['description']) : "Open a free examPAL account and crush the GMAT";

    ob_start();
    ?>

    <section class="newsletter_top nins single_nl">
        <div class="container">
            <div class="single_sign_up nl_head">
                <?php echo $title; ?>
            </div>
            <div class="single_info nl_right">
                <?php echo $description; ?>
            </div>
            <div class="nl_form_top">
                <form class="sign--up-form" method="post" autocomplete="off">
                    <div class="sign--up-fields">
                        <input type="email" value="" name="sign-email" required placeholder="Your Email Address..." autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                        <input type="password" value="" name="sign-password" required placeholder="Password" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                    </div>
                    <input type="submit" class="sign--up-submit submit" value="Start a free GMAT course" >
                    <div class="sign--up-request">Please wait..</div>
                    <div class="message"></div>
                </form>
            </div>
            <div class="cl"></div>
        </div>
    </section>

    <?php
    return ob_get_clean();
}

add_shortcode('sign_up_form', 'sign_up_form_func');

// Remove intercom created_at data
function remove_intercom_created_at($js){

    unset($js['created_at']);

    return $js;
}
add_filter('intercom_wp_js', 'remove_intercom_created_at');

// Fix function attachment_url_to_postid
function exampal_attachment_url_to_postid($post_id, $url) {

	global $wpdb;

	$dir = wp_get_upload_dir();
	$path = $url;

	$site_url = parse_url( $dir['url'] );
	$image_path = parse_url( $path );


	// We may have different upload dir url, so we should check it before scheme:
	if ( 0 === strpos( $path, 'http://blog.exampal.com/wp-content/uploads/' ) ) {
		$path = substr( $path, strlen( 'http://blog.exampal.com/wp-content/uploads/' ) );
	}

	//force the protocols to match if needed
	if ( isset( $image_path['scheme'] ) && ( $image_path['scheme'] !== $site_url['scheme'] ) ) {
		$path = str_replace( $image_path['scheme'], $site_url['scheme'], $path );
	}

	if ( 0 === strpos( $path, $dir['baseurl'] . '/' ) ) {
		$path = substr( $path, strlen( $dir['baseurl'] . '/' ) );
	}

	$sql = $wpdb->prepare(
		"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value = %s",
		$path
	);
	$post_id = $wpdb->get_var( $sql );

	return $post_id;
}
add_filter('attachment_url_to_postid', 'exampal_attachment_url_to_postid', 20, 2);