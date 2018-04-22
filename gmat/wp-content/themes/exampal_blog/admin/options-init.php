<?php

/**
  ReduxFramework Sample Config File
  For full documentation, please visit: https://docs.reduxframework.com
 * */

if (!class_exists('admin_folder_Redux_Framework_config')) {

    class admin_folder_Redux_Framework_config {

        public $args        = array();
        public $sections    = array();
        public $theme;
        public $ReduxFramework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            // This is needed. Bah WordPress bugs.  ;)
            if ( true == Redux_Helpers::isTheme( __FILE__ ) ) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

        }

        public function initSettings() {

            // Just for demo purposes. Not needed per say.
            $this->theme = wp_get_theme();

            // Set the default arguments
            $this->setArguments();

            // Set a few help tabs so you can see how it's done
            $this->setHelpTabs();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            // If Redux is running as a plugin, this will remove the demo notice and links
            add_action( 'redux/loaded', array( $this, 'remove_demo' ) );
            
            // Function to test the compiler hook and demo CSS output.
            // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
            add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 2);
            
            // Change the arguments after they've been declared, but before the panel is created
            //add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
            
            // Change the default value of a field after it's been set, but before it's been useds
            //add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );
            
            // Dynamically add a section. Can be also used to modify sections/fields
            //add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        /**

          This is a test function that will let you see when the compiler hook occurs.
          It only runs if a field	set with compiler=>true is changed.

         * */
        function compiler_action($options, $css) {
            //echo '<h1>The compiler hook has run!';
            //print_r($options); //Option values
            //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )

                          // Demo of how to use the dynamic CSS and write your own static CSS file
              $filename = dirname(__FILE__) . '/style' . '.css';
              global $wp_filesystem;
              if( empty( $wp_filesystem ) ) {
                require_once( ABSPATH .'/wp-admin/includes/file.php' );
              WP_Filesystem();
              }

              if( $wp_filesystem ) {
                $wp_filesystem->put_contents(
                    $filename,
                    $css,
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );
              }
        }

        /**

          Custom function for filtering the sections array. Good for child themes to override or add to the sections.
          Simply include this function in the child themes functions.php file.

          NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
          so you must use get_template_directory_uri() if you want to use any of the built in icons

         * */
        function dynamic_section($sections) {
            //$sections = array();
            $sections[] = array(
                'title' => __('Section via hook', 'website-options'),
                'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'website-options'),
                'icon' => 'el-icon-paper-clip',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array()
            );

            return $sections;
        }

        /**

          Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.

         * */
        function change_arguments($args) {
            //$args['dev_mode'] = true;

            return $args;
        }

        /**

          Filter hook for filtering the default value of any given field. Very useful in development mode.

         * */
        function change_defaults($defaults) {
            $defaults['str_replace'] = 'Testing filter hook!';

            return $defaults;
        }

        // Remove the demo link and the notice of integrated demo from the redux-framework plugin
        function remove_demo() {

            // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

                // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections() {

            /**
              Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
             * */
            // Background Patterns Reader
            $sample_patterns_path   = ReduxFramework::$_dir . '../sample/patterns/';
            $sample_patterns_url    = ReduxFramework::$_url . '../sample/patterns/';
            $sample_patterns        = array();

            if (is_dir($sample_patterns_path)) :

                if ($sample_patterns_dir = opendir($sample_patterns_path)) :
                    $sample_patterns = array();

                    while (( $sample_patterns_file = readdir($sample_patterns_dir) ) !== false) {

                        if (stristr($sample_patterns_file, '.png') !== false || stristr($sample_patterns_file, '.jpg') !== false) {
                            $name = explode('.', $sample_patterns_file);
                            $name = str_replace('.' . end($name), '', $sample_patterns_file);
                            $sample_patterns[]  = array('alt' => $name, 'img' => $sample_patterns_url . $sample_patterns_file);
                        }
                    }
                endif;
            endif;

            ob_start();

            $ct             = wp_get_theme();
            $this->theme    = $ct;
            $item_name      = $this->theme->get('Name');
            $tags           = $this->theme->Tags;
            $screenshot     = $this->theme->get_screenshot();
            $class          = $screenshot ? 'has-screenshot' : '';

            $customize_title = sprintf(__('Customize &#8220;%s&#8221;', 'website-options'), $this->theme->display('Name'));
            
            ?>
            <div id="current-theme" class="<?php echo esc_attr($class); ?>">
            <?php if ($screenshot) : ?>
                <?php if (current_user_can('edit_theme_options')) : ?>
                        <a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr($customize_title); ?>">
                            <img src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
                        </a>
                <?php endif; ?>
                    <img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
                <?php endif; ?>

                <h4><?php echo $this->theme->display('Name'); ?></h4>

                <div>
                    <ul class="theme-info">
                        <li><?php printf(__('By %s', 'website-options'), $this->theme->display('Author')); ?></li>
                        <li><?php printf(__('Version %s', 'website-options'), $this->theme->display('Version')); ?></li>
                        <li><?php echo '<strong>' . __('Tags', 'website-options') . ':</strong> '; ?><?php printf($this->theme->display('Tags')); ?></li>
                    </ul>
                    <p class="theme-description"><?php echo $this->theme->display('Description'); ?></p>
            <?php
            if ($this->theme->parent()) {
                printf(' <p class="howto">' . __('This <a href="%1$s">child theme</a> requires its parent theme, %2$s.') . '</p>', __('http://codex.wordpress.org/Child_Themes', 'website-options'), $this->theme->parent()->display('Name'));
            }
            ?>

                </div>
            </div>

            <?php
            $item_info = ob_get_contents();

            ob_end_clean();

            $sampleHTML = '';
            if (file_exists(dirname(__FILE__) . '/info-html.html')) {
                /** @global WP_Filesystem_Direct $wp_filesystem  */
                global $wp_filesystem;
                if (empty($wp_filesystem)) {
                    require_once(ABSPATH . '/wp-admin/includes/file.php');
                    WP_Filesystem();
                }
                $sampleHTML = $wp_filesystem->get_contents(dirname(__FILE__) . '/info-html.html');
            }

            // ACTUAL DECLARATION OF SECTIONS
            $this->sections[] = array(
                'title'     => __('Website settings', 'website-options'),
                'desc'      => __('Add Logos, favicons and icons, custom css, js code, footer text '),
                'icon'      => 'el-icon-home',
                // 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
                'fields'    => array(


   array(
                        'id'        => 'website-logo',
                        'type'      => 'media',
                        'url'       => true,
                        'title'     => __('Website logo', 'website-options'),
                        'compiler'  => 'true',
                        //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                        'desc'      => __('Upload your logo', 'website-options'),
                        'subtitle'  => __('Main website logo in the header', 'website-options'),
                        'default'   => array('url' => 'http://s.wordpress.org/style/images/codeispoetry.png'),
                        //'hint'      => array(
                        //    'title'     => 'Hint Title',
                        //    'content'   => 'This is a <b>hint</b> for the media field with a Title.',
                        //)
                    ),

      array(
                        'id'        => 'website-logo-footer',
                        'type'      => 'media',
                        'url'       => true,
                        'title'     => __('Website footer logo', 'website-options'),
                        'compiler'  => 'true',
                        //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                        'desc'      => __('Upload your logo', 'website-options'),
                        'subtitle'  => __('Logo to be placed in the footer', 'website-options'),
                        'default'   => array('url' => 'http://s.wordpress.org/style/images/codeispoetry.png'),
                        //'hint'      => array(
                        //    'title'     => 'Hint Title',
                        //    'content'   => 'This is a <b>hint</b> for the media field with a Title.',
                        //)
                    ),

   array(
                        'id'        => 'website-favicon',
                        'type'      => 'media',
                        'url'       => true,
                        'title'     => __('Favicon', 'website-options'),
                        'compiler'  => 'true',
                        //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                        'desc'      => __('Upload your favicon', 'website-options'),
                        'subtitle'  => __('Should be 16px x 16px', 'website-options'),
                        'default'   => array('url' => 'http://s.wordpress.org/style/images/codeispoetry.png'),
                        //'hint'      => array(
                        //    'title'     => 'Hint Title',
                        //    'content'   => 'This is a <b>hint</b> for the media field with a Title.',
                        //)
                    ),


                    array(
                        'id'        => 'website-anayltics',
                        'type'      => 'textarea',
                        'title'     => __('Tracking Code', 'website-options'),
                        'subtitle'  => __('Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.', 'website-options'),
                        'validate'  => 'js',
                        'desc'      => '',
                    ),

    
                    array(
                        'id'        => 'website-js-editor',
                        'type'      => 'ace_editor',
                        'title'     => __('JS Code', 'website-options'),
                        'subtitle'  => __('Paste your JS code here.', 'website-options'),
                        'mode'      => 'javascript',
                        'theme'     => 'chrome',
                        'desc'      => '',
                        'default'   => "jQuery(document).ready(function(){\n\n});"
                    ),
            
  array(
                        'id'        => 'address',
                        'type'      => 'textarea',
                        'title'     => __('Postal address', 'website-options'),
                        'subtitle'  => __('', 'website-options'),
                        'validate' => 'html_custom',
                        'default' => '<br />Some HTML is allowed in here.<br />',
                    'allowed_html' => array(
        'a' => array(
            'href' => array(),
            'title' => array()
        ),
        'br' => array(),
        'em' => array(),
        'strong' => array()
    )
                    ),

                    array(
                        'id'        => 'footer-text',
                        'type'      => 'editor',
                        'title'     => __('Footer Text', 'website-options'),
                        'subtitle'  => __('You can use the following shortcodes in your footer text: [wp-url] [site-url] [theme-url] [login-url] [logout-url] [site-title] [site-tagline] [current-year]', 'website-options'),
                        'default'   => '&copy; [current-year] Some text to go at the bottom of the page',
                    ),
                )
            );

            $this->sections[] = array(
                'icon'      => 'el-icon-website',
                'title'     => __('Styling Options', 'website-options'),
                'fields'    => array(
                             array(
                        'id'            => 'opt-typography',
                        'type'          => 'typography',
                        'title'         => __('Body typography', 'redux-framework-demo'),
                        //'compiler'      => true,  // Use if you want to hook in your own CSS compiler
                        'google'        => true,    // Disable google fonts. Won't work if you haven't defined your google api key
                        'font-backup'   => true,    // Select a backup non-google font in addition to a google font
                        //'font-style'    => false, // Includes font-style and weight. Can use font-style or font-weight to declare
                        //'subsets'       => false, // Only appears if google is true and subsets not set to false
                        //'font-size'     => false,
                        //'line-height'   => false,
                        //'word-spacing'  => true,  // Defaults to false
                        //'letter-spacing'=> true,  // Defaults to false
                        //'color'         => false,
                        //'preview'       => false, // Disable the previewer
                        'all_styles'    => true,    // Enable all Google Font style/weight variations to be added to the page
                        'compiler'      => array('body'), // An array of CSS selectors to apply this font style to dynamically
                        'units'         => 'px', // Defaults to px
                        'subtitle'      => __('Typography option for the main body copy.', 'redux-framework-demo'),
                        'default'       => array(
                            'color'         => '#333',
                            'font-style'    => '400',
                            'font-family'   => 'Open Sans',
                            'google'        => true,
                            'font-size'     => '18px',
                            'line-height'   => '24px'),
                        'preview' => array('text' => 'This is a preview of how your text will appear on the site'),
                ),


                             array(
                        'id'            => 'heading-typography',
                        'type'          => 'typography',
                        'title'         => __('Heading typography', 'redux-framework-demo'),
                        //'compiler'      => true,  // Use if you want to hook in your own CSS compiler
                        'google'        => true,    // Disable google fonts. Won't work if you haven't defined your google api key
                        'font-backup'   => false,    // Select a backup non-google font in addition to a google font
                        //'font-style'    => false, // Includes font-style and weight. Can use font-style or font-weight to declare
                        'subsets'       => false, // Only appears if google is true and subsets not set to false
                        'font-size'     => false,
                        'line-height'   => false,
                                                'text-transform' => true, 
                        //'word-spacing'  => true,  // Defaults to false
                        //'letter-spacing'=> true,  // Defaults to false
                        //'color'         => false,
                        //'preview'       => false, // Disable the previewer
                        'all_styles'    => true,    // Enable all Google Font style/weight variations to be added to the page
                        'compiler'      => array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'), // An array of CSS selectors to apply this font style to dynamically
                        'units'         => 'px', // Defaults to px
                        'subtitle'      => __('Typography option for heading styles.', 'redux-framework-demo'),
                        'default'       => array(
                            'color'         => '#333',
                            'font-style'    => '700',
                            'font-family'   => 'Open Sans',
                            'google'        => true,
                           ),
                        'preview' => array('text' => 'This is a preview of how your text will appear on the site'),
                ),

 array(
                        'id'            => 'nav-typography',
                        'type'          => 'typography',
                        'title'         => __('Navigation typography', 'redux-framework-demo'),
                        //'compiler'      => true,  // Use if you want to hook in your own CSS compiler
                        'google'        => true,    // Disable google fonts. Won't work if you haven't defined your google api key
                        'font-backup'   => true,    // Select a backup non-google font in addition to a google font
                        //'font-style'    => false, // Includes font-style and weight. Can use font-style or font-weight to declare
                        //'subsets'       => false, // Only appears if google is true and subsets not set to false
                        //'font-size'     => false,
                        //'line-height'   => false,
                        //'word-spacing'  => true,  // Defaults to false
                        //'letter-spacing'=> true,  // Defaults to false
                        //'color'         => false,
                        //'preview'       => false, // Disable the previewer
                        'text-transform' => true, 
                        'all_styles'    => true,    // Enable all Google Font style/weight variations to be added to the page
                        'compiler'      => array('nav ul li a'), // An array of CSS selectors to apply this font style to dynamically
                        'units'         => 'px', // Defaults to px
                        'subtitle'      => __('Typography option for the main body copy.', 'redux-framework-demo'),
                        'default'       => array(
                            'color'         => '#333',
                            'font-style'    => '400',
                            'font-family'   => 'Open Sans',
                            'google'        => true,
                            'font-size'     => '18px',
                            'line-height'   => '24px'),
                        'preview' => array('text' => 'This is a preview of how your text will appear on the site'),
                ),

                               array(
                        'id'        => 'opt-link-color',
                        'type'      => 'link_color',
                        'title'     => __('Links Color Option', 'website-options'),
                        'subtitle'  => __('Only color validation can be done on this field type', 'website-options'),
                        'desc'      => __('This is the description field, again good for additional info.', 'website-options'),
                        'regular'   => true, // Disable Regular Color
                        'hover'     => true, // Disable Hover Color
                        'active'    => true, // Disable Active Color
                        //'visited'   => true,  // Enable Visited Color
                        'default'   => array(
                            'regular'   => '#aaa',
                            'hover'     => '#bbb',
                            'active'    => '#ccc',
                        ),
                        'output'        => array(
                            'regular'   => 'a',
                            'hover'     => 'a:hover',
                            'active'    => 'a:active',
                            ), 
                    ),
                    array(
                        'id'        => 'opt-background',
                        'type'      => 'background',
                        'compiler'    => array('body'),
                        'title'     => __('Body Background', 'website-options'),
                        'subtitle'  => __('Body background with image, color, etc.', 'website-options'),
                        'default'   => '#FFFFFF',
                    ),
             
                  

                
                   
            
                    array(
                        'id'        => 'opt-custom-css',
                        'type'      => 'textarea',
                        'title'     => __('Custom CSS', 'website-options'),
                        'subtitle'  => __('Quickly add some CSS to your theme by adding it to this block.', 'website-options'),
                        'desc'      => __('', 'website-options'),
                        'validate'  => 'css',
                    ),
                   
                )
            );

           

            $theme_info  = '<div class="redux-framework-section-desc">';
            $theme_info .= '<p class="redux-framework-theme-data description theme-uri">' . __('<strong>Theme URL:</strong> ', 'website-options') . '<a href="' . $this->theme->get('ThemeURI') . '" target="_blank">' . $this->theme->get('ThemeURI') . '</a></p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-author">' . __('<strong>Author:</strong> ', 'website-options') . $this->theme->get('Author') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-version">' . __('<strong>Version:</strong> ', 'website-options') . $this->theme->get('Version') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-description">' . $this->theme->get('Description') . '</p>';
            $tabs = $this->theme->get('Tags');
            if (!empty($tabs)) {
                $theme_info .= '<p class="redux-framework-theme-data description theme-tags">' . __('<strong>Tags:</strong> ', 'website-options') . implode(', ', $tabs) . '</p>';
            }
            $theme_info .= '</div>';

            if (file_exists(dirname(__FILE__) . '/../README.md')) {
                $this->sections['theme_docs'] = array(
                    'icon'      => 'el-icon-list-alt',
                    'title'     => __('Documentation', 'website-options'),
                    'fields'    => array(
                        array(
                            'id'        => '17',
                            'type'      => 'raw',
                            'markdown'  => true,
                            'content'   => file_get_contents(dirname(__FILE__) . '/../README.md')
                        ),
                    ),
                );
            }
            

            $this->sections[] = array(
                'title'     => __('Import / Export', 'website-options'),
                'desc'      => __('Import and Export your Redux Framework settings from file, text or URL.', 'website-options'),
                'icon'      => 'el-icon-refresh',
                'fields'    => array(
                    array(
                        'id'            => 'opt-import-export',
                        'type'          => 'import_export',
                        'title'         => 'Import Export',
                        'subtitle'      => 'Save and restore your Redux options',
                        'full_width'    => false,
                    ),
                ),
            );                     
                    
            $this->sections[] = array(
                'type' => 'divide',
            );

            $this->sections[] = array(
                'icon'      => 'el-icon-info-sign',
                'title'     => __('Theme Information', 'website-options'),
                'desc'      => __('<p class="description">This is the Description. Again HTML is allowed</p>', 'website-options'),
                'fields'    => array(
                    array(
                        'id'        => 'opt-raw-info',
                        'type'      => 'raw',
                        'content'   => $item_info,
                    )
                ),
            );

            if (file_exists(trailingslashit(dirname(__FILE__)) . 'README.html')) {
                $tabs['docs'] = array(
                    'icon'      => 'el-icon-book',
                    'title'     => __('Documentation', 'website-options'),
                    'content'   => nl2br(file_get_contents(trailingslashit(dirname(__FILE__)) . 'README.html'))
                );
            }
        }

        public function setHelpTabs() {

            // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-1',
                'title'     => __('Theme Information 1', 'website-options'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'website-options')
            );

            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-2',
                'title'     => __('Theme Information 2', 'website-options'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'website-options')
            );

            // Set the help sidebar
            $this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'website-options');
        }

        /**

          All the possible arguments for Redux.
          For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

         * */
        public function setArguments() {

            $theme = wp_get_theme(); // For use with some settings. Not necessary.

            $this->args = array(
                'opt_name' => 'website_options',
                'display_name' => 'Options',
                'display_version' => false,
                'page_slug' => '_options',
                'page_title' => 'Options',
                'update_notice' => true,
                'intro_text' => '<p>Update the colour, typography, layout, logo etc</p>',
                'footer_text' => '<p>Website options by Maven Design - mavendesign.co.uk</p>',
                'admin_bar' => true,
                'menu_type' => 'menu',
                'menu_title' => 'Options',
                'allow_sub_menu' => true,
                'page_parent_post_type' => 'your_post_type',
                'customizer' => true,
                'default_mark' => '*',
                'hints' => 
                array(
                  'icon' => 'el-icon-question-sign',
                  'icon_position' => 'right',
                  'icon_size' => 'normal',
                  'tip_style' => 
                  array(
                    'color' => 'light',
                  ),
                  'tip_position' => 
                  array(
                    'my' => 'top left',
                    'at' => 'bottom right',
                  ),
                  'tip_effect' => 
                  array(
                    'show' => 
                    array(
                      'duration' => '500',
                      'event' => 'mouseover',
                    ),
                    'hide' => 
                    array(
                      'duration' => '500',
                      'event' => 'mouseleave unfocus',
                    ),
                  ),
                ),
                'output' => true,
                'output_tag' => true,
                'compiler' => true,
                'page_icon' => 'icon-themes',
                'page_permissions' => 'manage_options',
                'save_defaults' => true,
                'show_import_export' => true,
                'transient_time' => '3600',
                'network_sites' => true,
              );

          
        }

    }
    
    global $reduxConfig;
    $reduxConfig = new admin_folder_Redux_Framework_config();
}

/**
  Custom function for the callback referenced above
 */
if (!function_exists('admin_folder_my_custom_field')):
    function admin_folder_my_custom_field($field, $value) {
        print_r($field);
        echo '<br/>';
        print_r($value);
    }
endif;

/**
  Custom function for the callback validation referenced above
 * */
if (!function_exists('admin_folder_validate_callback_function')):
    function admin_folder_validate_callback_function($field, $value, $existing_value) {
        $error = false;
        $value = 'just testing';

        /*
          do your validation

          if(something) {
            $value = $value;
          } elseif(something else) {
            $error = true;
            $value = $existing_value;
            $field['msg'] = 'your custom error message';
          }
         */

        $return['value'] = $value;
        if ($error == true) {
            $return['error'] = $field;
        }
        return $return;
    }
endif;
