<?php

// create custom plugin settings menu
add_action('admin_menu', 'exampal_plan_plugin_create_menu');

function exampal_plan_plugin_create_menu() {

    add_menu_page('Exampal Plan Calendar Settings', 'Exampal Plan Calendar', 'administrator', 'exampal-plan-calendar-settings', 'admin_exampal_plan_render_options');

    //call register settings function
    add_action( 'admin_init', 'register_exampal_settings' );
}


function register_exampal_settings() {
    //register our settings
    register_setting( 'exampal-plan-settings-group', 'exampal_form_title' );
    register_setting( 'exampal-plan-settings-group', 'exampal_form_subtitle' );
    register_setting( 'exampal-plan-settings-group', 'exampal_form_description' );

    register_setting( 'exampal-plan-settings-group', 'exampal_calendar_title' );
    register_setting( 'exampal-plan-settings-group', 'exampal_calendar_description' );

    register_setting( 'exampal-plan-settings-group', 'exampal_addevent_token' );
    register_setting( 'exampal-plan-settings-group', 'exampal_page_for_plan' );
    register_setting( 'exampal-plan-settings-group', 'exampal_mailchimp_api_key' );
    register_setting( 'exampal-plan-settings-group', 'exampal_mailchimp_lists' );
}

function admin_exampal_plan_render_options() {
    $ex_mc4wp_key = exampal_get_mc4wp_api_key();
    $ex_options_key = get_option('exampal_mailchimp_api_key');
    $ex_options_addevent_key = get_option('exampal_addevent_token');

    $ex_lists = exampal_get_mailchimp_lists();

    $lists_str = get_option('exampal_mailchimp_lists');
    $lists_arr = explode(',',$lists_str);
    ?>
    <div class="wrap">
        <h2><?php _e('Plan Calendar Options', 'exampal'); ?></h2>

        <form method="post" action="options.php">
            <?php settings_fields( 'exampal-plan-settings-group' ); ?>

            <h2><?php _e('Plugin options', 'exampal'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Page for calendar', 'exampal'); ?>
                        <p class="description" style="font-weight: normal;"><?php _e('Choose the page for displaying calendar.', 'exampal'); ?></p>
                    </th>
                    <td>
                        <?php
                            $ex_page_for_plan = get_option('exampal_page_for_plan');
                            $ex_all_pages = get_all_page_ids();
                        ?>
                        <select id="exampal_page_for_plan" class="post_form" name="exampal_page_for_plan">
                            <option value=""><?php _e('Not selected', 'exampal'); ?></option>
                            <?php
                                foreach($ex_all_pages as $page_id) {
                                    ?>
                                        <option value="<?php echo $page_id; ?>" <?php selected( $ex_page_for_plan, $page_id); ?>><?php echo get_the_title($page_id); ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                        <p class="help">
                            <?php _e('Page "gmat-study-planner-calendar" generetes automatically on plugin activation. But can be changed here anytime.', 'exampal'); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <h2><?php _e('Form shortcode settings', 'exampal'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Form shortcode Title', 'exampal'); ?></th>
                    <td>
                        <input type="text" class="widefat" name="exampal_form_title" value="<?php echo get_option('exampal_form_title'); ?>">
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Form shortcode Subtitle', 'exampal'); ?></th>
                    <td>
                        <input type="text" class="widefat" name="exampal_form_subtitle" value="<?php echo get_option('exampal_form_subtitle'); ?>">
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Form shortcode description', 'exampal'); ?></th>
                    <td>
                        <textarea class="widefat" name="exampal_form_description" value="<?php echo get_option('exampal_form_description'); ?>"><?php echo get_option('exampal_form_description'); ?></textarea>
                    </td>
                </tr>
            </table>

            <h2><?php _e('Calendar shortcode settings', 'exampal'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Calendar shortcode title', 'exampal'); ?></th>
                    <td>
                        <input type="text" class="widefat" name="exampal_calendar_title" value="<?php echo get_option('exampal_calendar_title'); ?>">
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Calendar shortcode description', 'exampal'); ?></th>
                    <td>
                        <textarea class="widefat" name="exampal_calendar_description" value="<?php echo get_option('exampal_calendar_description'); ?>"><?php echo get_option('exampal_calendar_description'); ?></textarea>
                    </td>
                </tr>
            </table>

            <h2><?php _e('Mailchimp settings', 'exampal'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Mailchimp api key', 'exampal'); ?>
                        <p class="description" style="font-weight: normal;"><?php _e('The API key for connecting with your MailChimp account.', 'exampal'); ?></p>
                    </th>
                    <td>
                        <?php
                        if ($ex_mc4wp_key) {
                            ?>
                            <input id="exampal_mailchimp_api_key_obfuscated" type="text" readonly class="widefat" name="exampal_mailchimp_api_key_mc4wp" value="<?php echo  exampal_obfuscate_string($ex_mc4wp_key); ?>">
                            <input type="hidden" class="widefat" name="exampal_mailchimp_api_key" value="<?php echo  $ex_mc4wp_key; ?>">
                            <?php
                        } elseif ($ex_options_key) {
                            $ex_api_key_obfuscated = exampal_obfuscate_string($ex_options_key);
                            ?>
                            <input id="exampal_mailchimp_api_key" type="hidden" class="widefat" name="exampal_mailchimp_api_key" value="<?php echo $ex_options_key; ?>">
                            <input id="exampal_mailchimp_api_key_obfuscated" type="text" class="widefat" name="exampal_mailchimp_api_key_obfuscated" value="<?php echo $ex_api_key_obfuscated; ?>">
                            <?php
                        } else {
                            ?>
                            <input type="text" class="widefat" name="exampal_mailchimp_api_key" value="">
                            <?php
                        }
                        ?>
                        <p class="help">
                            <?php _e('Uses "MailChimp for WP" plugin api key option if activated.', 'exampal'); ?>
                        </p>
                    </td>
                </tr>


                <tr valign="top">
                    <th scope="row"><?php _e('Select Lists', 'exampal'); ?>
                        <p class="description" style="font-weight: normal;"><?php _e('Lists to subscribe users with form.', 'exampal'); ?></p>
                    </th>
                    <td>
                        <?php
                        if ($ex_lists->total_items) {
                            ?>
                            <ul class="mnt-checklist" id="exampal_mailchimp_lists_checkboxes" >
                                <?php
                                foreach ($ex_lists->lists as $ex_list) {
                                    $checked = ' ';
                                    if (in_array($ex_list->id, $lists_arr)) {
                                        $checked = 'checked="checked"';
                                    }
                                    ?>
                                    <li>
                                        <?php echo '<input type="checkbox" name="exampal_mailchimp_lists_'.$ex_list->id.'" value="'.$ex_list->id.'" '.$checked.' />'.$ex_list->name; ?>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>
                        <input id="exampal_mailchimp_lists" type="hidden" class="widefat" name="exampal_mailchimp_lists" value="<?php echo $lists_str; ?>">
                        <p class="help">
                            <?php _e('Enter api key before and press save button to lists appear.', 'exampal'); ?>
                        </p>
                    </td>
                </tr>


            </table>
            <h2><?php _e('AddEvent settings', 'exampal'); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('AddEvent api token', 'exampal'); ?>
                        <p class="description" style="font-weight: normal;"><?php _e('The API token for connecting with your AddEvent account.', 'exampal'); ?></p>
                    </th>
                    <td>
                        <input type="text" class="widefat" name="exampal_addevent_token" value="<?php echo $ex_options_addevent_key;?>">
                    </td>
                </tr>
                
            </table>
            <?php submit_button(); ?>

        </form>
    </div>
    <div class="wrap">
        <h2><?php _e('Spreadsheet', 'exampal'); ?></h2>

        <form method="post" enctype="multipart/form-data" action="<?php echo admin_url( 'admin.php?page=exampal-plan-calendar-settings' ); ?>">
            <?php wp_nonce_field( 'exampal_upload_spreadsheet_nonce', 'upload_spreadsheet_nonce' );?>
            <input name="action" value="upload_spreadsheet" type="hidden">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Upload spreadsheet', 'exampal'); ?>
                        <p class="description" style="font-weight: normal;"><?php _e('.xlsx', 'exampal'); ?></p>
                    </th>
                    <td>
                        <input type="file" name="exampal_upload_spreadsheet">
                        <p class="help">
                            <?php _e('', 'exampal'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <input type="submit" class="button button-primary" value="Update spreadsheet" />
        </form>
    </div>
    <?php

    exampal_render_admin_spreadsheet();

}

/* Get data from xlsx file and save it to options */
function exampal_upload_spreadsheet_handle(){
    if ( isset($_POST['action']) && ($_POST['action'] == 'upload_spreadsheet') && current_user_can( 'manage_options')) {
        // verify nonce
        if( !isset( $_POST['upload_spreadsheet_nonce'] ) || !wp_verify_nonce( $_POST['upload_spreadsheet_nonce'], 'exampal_upload_spreadsheet_nonce' ) ) return;

        $allowed_file_types = array('xlsx', 'xls');
        $file_ext = pathinfo($_FILES['exampal_upload_spreadsheet']['name'], PATHINFO_EXTENSION);

        require_once(EXAMPAL_DIR.'/includes/libs/PHPExcel.php');
        require_once(EXAMPAL_DIR.'/includes/libs/exampalRichTextToHtml.php');

        $filename = $_FILES['exampal_upload_spreadsheet']['tmp_name'];
        if (!file_exists($filename) || !in_array($file_ext, $allowed_file_types)) return;

        $type = PHPExcel_IOFactory::identify($filename);
        $objReader = PHPExcel_IOFactory::createReader($type);
        $objPHPExcel = $objReader->load($filename);

        $richtextService = new RichTextService();

        $worksheets = array();
        $custom_worksheets = array();

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $worksheet_arr = array();
            $maxCol = $worksheet->getHighestColumn();
            $maxRow = $worksheet->getHighestRow();
            $maxCol++;
            $maxRow++;

            $rRef = -1;
            for ($row = 1; $row <= $maxRow; ++$row) {
                $rRef++;
                $cRef = -1;
                for ($col = 'A'; $col != $maxCol; ++$col) {
                    $cRef++;
                    $cell = $worksheet->getCell($col.$row);
                    if ($cell->getValue() !== null) {
                        if ($cell->getValue() instanceof PHPExcel_RichText) {
                            $worksheet_arr[$rRef][$cRef] = $richtextService->getHTML($cell->getValue());
                        } else {
                            $worksheet_arr[$rRef][$cRef] = $cell->getValue();
                        }
                    } else {
                        $worksheet_arr[$rRef][$cRef] = null;
                    }
                }
            }

            $worksheets[$worksheet->getTitle()] = $worksheet_arr;
        }

        update_option('exampal_plan_spreadsheet_array', exampal_prepare_array($worksheets));
    }
}
add_action('wp_loaded', 'exampal_upload_spreadsheet_handle');

function exampal_render_admin_spreadsheet(){
    $ex_spreadsheet_array = get_option('exampal_plan_spreadsheet_array');
    if (is_array($ex_spreadsheet_array) && !empty($ex_spreadsheet_array)) {
        ?>
        <div id="exampalAdminSpreadsheet" class="wrap">
            <h2><?php _e('Shortcode', 'exampal'); ?></h2>                
                <table>
                    <tr>
                        <th>Plan form</th>
                        <th width="10%";></th>
                        <th>Calendar</th>
                    </tr>
                    <tr>
                        <td><p>[exampal_plan_form plan="n"]</p></td>
                        <td></td>
                        <td><p>[exampal_plan_calendar]</p></td>
                    </tr>
                    <tr>
                        <td colspan="3"><p class="description">n - list plan number</p></td>
                    </tr>

                   <?php 
                  
                   if(!empty($ex_spreadsheet_array)):
                   $i= 1;
                   foreach($ex_spreadsheet_array as $key => $sheet): ?>
                        <tr>
                            <td colspan="3"><p class="description"><?php echo $i . ' - ' . $key; ?></p></td>
                        </tr>
                    <?php 
                    $i++;
                    endforeach; endif; ?>
                    
                </table>
            <h2><?php _e('Current Spreadsheet', 'exampal'); ?></h2>
            <h2 class="nav-tab-wrapper">
                <?php
                $tab_count = 0;
                foreach($ex_spreadsheet_array as $key => $sheet) {
                    ?>
                    <a class="nav-tab exampal-tab-button <?php echo ($tab_count == 0) ? 'nav-tab-active' : ''; ?>" href="#" data-exampal-tab="<?php echo $key; ?>"><?php echo $key; ?></a>
                    <?php
                    $tab_count++;
                }
                ?>
            </h2>
            <div id="exampalAdminSpreadsheetTabs">
                <?php
                $tab_count = 0;
                foreach($ex_spreadsheet_array as $key => $sheet) {
                    ?>
                    <div class="exampal-tab" data-exampal-tab="<?php echo $key; ?>" style="display: <?php echo ($tab_count == 0) ? 'block' : 'none'; ?>;">
                        <table class="widefat fixed" style="border-collapse: collapse;">
                            <?php
                            foreach($sheet as $key => $row) {
                                if (is_array($row) && !empty($row)){
                                    if ($key == 0) {
                                        ?>
                                        <thead>
                                            <tr>
                                                <?php
                                                foreach($row as $key => $cell) {
                                                    ?>
                                                    <th class="manage-column column-columnname" style="border: 1px solid #e1e1e1; font-weight: bold;"><?php echo $cell; ?></th>
                                                    <?php
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                    } else {
                                        ?>
                                            <tr>
                                                <?php
                                                foreach ($row as $key => $cell) {
                                                    ?>
                                                    <td class="column-columnname" style="border: 1px solid #e1e1e1;">
                                                        <?php echo $cell; ?>
                                                    </td>
                                                    <?php
                                                }
                                                ?>
                                            </tr>
                                        <?php
                                        if ($key == count($sheet) - 1) echo '</tbody>';
                                    }
                                }
                            }
                            ?>
                        </table>
                    </div>
                    <?php
                    $tab_count++;
                }
                ?>
            </div>

        </div>
        <?php

    }
}