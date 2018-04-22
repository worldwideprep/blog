<?php


/* Subscription form shortcode */
function exampal_plan_form_shortcode($atts)
{
    extract(shortcode_atts(array('plan'=>0), $atts));

    ob_start();
    ?>
    <div id="custombanner" class="plan-form-shortcode">

        <table width="90%" align="center" border="0">
            <tr>
                <td id="custom2colmn" colspan="2" >
                    <h1><?php echo get_option('exampal_form_title'); ?></h1>
                    <h2><?php echo get_option('exampal_form_subtitle'); ?></h2>
                    <p><?php echo get_option('exampal_form_description'); ?></p>

                </td>
                <td id="custom1colmn" align="center">
                    <img id="customdesktop" src="<?php echo EXAMPAL_PATH; ?>/assets/img/plan_blog.png"/>
                </td>
            </tr>
        </table>

        <div id="custominputbg">
            <form id="exampal-plan-form" action="<?php echo get_permalink(get_option('exampal_page_for_plan')); ?>" method="post" target="_blank">
                <?php wp_nonce_field( 'exampal_mc_subscibe_nonce', 'exampal_mc_subscibe' );?>
                <input type="hidden" name="plan" value="<?php echo $plan; ?>">
                <table width="90%" align="center" border="0">
                    <tr>
                        <td style="border:none; background:none;">
                            <input style="" name="exampal-plan-date" placeholder="Select Start Date" type='text' class='txtDate'/>
                        </td>
                        <td style="border:none;">
                            <div class="input-wrap">
                                <input id="customemailinputfield" style="" value="" name="exampal-plan-email" required="" placeholder="Enter your email" type="email">
                            </div>
                        </td>

                        <td style="border:none;" align="center">
                            <input id="customsubmitinputfield" style="" class="submit" value="CREATE MY STUDY PLAN!" type="submit">
                        </td>
                    </tr>
                </table>

            </form>
        </div>
        <div id="custommobile">
            <img align="middle" src="http://blog.exampal.com/wp-content/uploads/2017/05/plan_blog.png" />
        </div>

    </div>
    <?php
    $out = ob_get_clean();

    return $out;
}

add_shortcode('exampal_plan_form', 'exampal_plan_form_shortcode');


/* Subscription form shortcode */
function exampal_plan_calendar_shortcode($atts)
{
    extract(shortcode_atts(array(), $atts));
   
    ob_start();
    if(empty($_POST['exampal-plan-date'])){
        $_POST['exampal-plan-date'] = date("F j, Y");
    }

    if(empty($_POST['plan'])){
        $_POST['plan'] = 1;
    }

     $arr = get_option('exampal_plan_spreadsheet_array');

    $addevent_token = get_option('exampal_addevent_token');

    $ics_file_link_encoded = get_permalink(get_option('exampal_page_for_plan')).urlencode('?calendar-export=ics&date='.urlencode($_POST['exampal-plan-date']).'&plan='.$_POST['plan']);
    $ics_highlighted_file_link_encoded = get_permalink(get_option('exampal_page_for_plan')).urlencode('?calendar-export=ics-highlighted&date='.urlencode($_POST['exampal-plan-date']).'&plan='.$_POST['plan']);

    ?>
    <div class="calendar-section">
        <div id="custombanner" class="exampal-calendar-shortcode row fullscreen">

            <div class="container">
                <table class="table-banner" style="border:none;" border="0" align="center">
                    <tbody>
                    <tr style="border:none;">
                        <td class="formobile col-1">
                            <h1><?php echo get_option('exampal_calendar_title'); ?></h1>
                            <p><?php echo get_option('exampal_calendar_description'); ?></p>

                        </td>
                        <td class="formobile col-2" align="center">
                            <img id="customdesktop1" src="<?php echo EXAMPAL_PATH; ?>/assets/img/plan_blog_bigger.png">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="custominputbg" style="padding:16px 0;"></div>


      
        </div>

        <div id="calendarheadermobile">
         <form method="POST">
          <?php wp_nonce_field( 'exampal_add_calendar_nonce', 'exampal_add_calendar' );?>
            <table align="center">
                <tr>
                    <td>&nbsp;<span class="customcalendarhead1" data-date = '<?php echo $_POST['exampal-plan-date']?>'><?php echo $_POST['exampal-plan-date']?></span></td>
                    <td style="vertical-align:middle;">
                        <img class="custombutton" src="<?php echo EXAMPAL_PATH; ?>/assets/img/btns.png"/></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <ul class="custommobiledropdown">
                            <li id="parent_dropdowndatefield"><span class="customcalendarhead5 loader-image"><img
                                            src="<?php echo EXAMPAL_PATH; ?>/assets/img/Page-1.png"/> Change Date </span>
                            </li>
                            <li id="dropdowndatefield"><span class="customcalendarhead5">
                            <input name="exampal-plan-date" style="" placeholder="Enter New Start Date" type='text' class='txtDate' value="<?php echo $_POST['exampal-plan-date']; ?>"/></span>
                            </li>
                            <li>
                                <span class="customcalendarhead6 csv-button">
                                    <img src="<?php echo EXAMPAL_PATH; ?>/assets/img/download.png"/>
                                    <a href="#" data-href="<?php echo EXAMPAL_PATH; ?>/includes/exampal-export-csv.php">Download</a>
                                </span>
                                <span class="csv-loading">
                                    <img src="<?php echo EXAMPAL_PATH; ?>/assets/img/Page-1.png"/> Loading
                                </span>
                            </li>
                            <!--<li id="parent_dropdowndatefield2"><span class="customcalendarhead2"><img
                                            src="<?php /*echo EXAMPAL_PATH; */?>/assets/img/cal-copy.png"/> Add to Calendar</span>
                            </li>-->
                            <!--<ul id="customdropdowncalendar">-->
                                <li>
                                <?php if (!empty($addevent_token)) : ?>
                                    <span class="export-button-wrap">
                                        <img src="<?php echo EXAMPAL_PATH; ?>/assets/img/cal-copy.png"/>
                                        <!--<select name="addtocalendar" class="selectpicker addcalendar_desk">
                                            <option disabled selected>Add to calendar</option>
                                            <option value="google">Google Calendar</option>
                                            <option value="outlookcom">Outlook Calendar</option>
                                        </select>-->
                                        <!--<a class="add-to-google" href="https://calendar.google.com/calendar/render?cid=<?php /*echo $ics_file_link_encoded; */?>" target="_blank" title="Add to Google Calendar">Add to Google Calendar</a>-->
                                        <select name="calendar-export-google" class="selectpicker calendar-export-google">
                                            <option disabled selected>Add to Google Calendar</option>
                                            <option value="https://calendar.google.com/calendar/render?cid=<?php echo $ics_file_link_encoded; ?>">Add whole calendar</option>
                                            <option value="https://calendar.google.com/calendar/render?cid=<?php echo $ics_highlighted_file_link_encoded; ?>">Add highlighted events</option>
                                        </select>
                                    </span>
                                    <span class="export-loading">
                                        <img src="<?php echo EXAMPAL_PATH; ?>/assets/img/Page-1.png"/> Loading
                                    </span>
                                <?php endif; ?>
                                </li>

                                <li>
                                    <img src="<?php echo EXAMPAL_PATH; ?>/assets/img/time.png"/>
                                    <?php $keys = array_keys($arr);?>
                                    <select id="customselectionmenu" class="selectpicker plan-list" name="plan" >
                                        <?php foreach ($keys as $key => $value):
                                            $key = $key + 1;
                                            ?>
                                            <option value="<?php echo $key;?>" <?php if($_POST['plan'] == $key){ echo 'selected="selected"';}?>><?php echo $value; ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </li>
                        </ul>
                    </td>
                </tr>
            </table>
            </form>
        </div>


        <div id="calendarheader">
            <form method="POST">
             <?php wp_nonce_field( 'exampal_add_calendar_nonce', 'exampal_add_calendar' );?>
                <table style="width: 98%;margin-bottom:25px;" align="center">
                    <tbody>
                    <tr id="customtblhead">
                        <td align="left" width="24%" height="">
                            <span class="customcalendarhead1"><?php echo $_POST['exampal-plan-date']; ?></span>
                        </td>
                        <td align="center" width="14%">
                            <span class="customcalendarhead2">
                                <img src="<?php echo EXAMPAL_PATH; ?>/assets/img/time.png"/>
                                <?php $keys = array_keys($arr);?>
                                <select id="customselectionmenu" class="selectpicker plan-list" name="plan" >
                                    <?php foreach ($keys as $key => $value): 
                                    $key = $key + 1;
                                    ?>
                                        <option value="<?php echo $key;?>" <?php if($_POST['plan'] == $key){ echo 'selected="selected"';}?>><?php echo $value; ?></option>
                                    <?php endforeach ?>
                                </select>
                            </span>
                        </td>
                        <td align="center" width="5%"></td>
                        <td align="center" width="5%"></td>
                        <td align="center" width="14%">                    
                            <ul class="parent_dropdowndatefield">
                            <li id="parent_dropdowndatefield4"><span class="customcalendarhead5">
                                <img src="<?php echo EXAMPAL_PATH; ?>/assets/img/Page-1.png"/> Change Date </span>
                            </li>
                            <li id="dropdowndatefield5"><span class="customcalendarhead5">
                            <input name="exampal-plan-date" style="" placeholder="Enter New Start Date" type='text' class='txtDate' value="<?php echo $_POST['exampal-plan-date']; ?>"/></span>
                            </li>
                            </ul>
                            
                        </td>
                        <td align="center" width="14%">
                            <span class="customcalendarhead6 csv-button">
                                <img src="<?php echo EXAMPAL_PATH; ?>/assets/img/download.png"/>
                                <a  href="#">Download</a>
                            </span>
                            <span class="csv-loading">
                                <img src="<?php echo EXAMPAL_PATH; ?>/assets/img/Page-1.png"/> Loading
                            </span>
                        </td>
                        <td align="center" width="22%">
                            <?php if (!empty($addevent_token)) : ?>
                                <span class="customcalendarhead7 export-button-wrap">
                                    <img src="<?php echo EXAMPAL_PATH; ?>/assets/img/cal-copy.png"/>
                                    <!--<select name="addtocalendar" class="selectpicker addcalendar_desk">
                                        <option disabled selected>Add to calendar</option>
                                        <option value="google">Google Calendar</option>
                                        <option value="outlookcom">Outlook Calendar</option>
                                    </select>-->
                                    <!--<a class="add-to-google" href="https://calendar.google.com/calendar/render?cid=<?php /*echo $ics_file_link_encoded; */?>" target="_blank" title="Add to Google Calendar">Add to Google Calendar</a>-->
                                    <select name="calendar-export-google" class="selectpicker calendar-export-google">
                                        <option disabled selected>Add to Google Calendar</option>
                                        <option value="https://calendar.google.com/calendar/render?cid=<?php echo $ics_file_link_encoded; ?>">Add whole calendar</option>
                                        <option value="https://calendar.google.com/calendar/render?cid=<?php echo $ics_highlighted_file_link_encoded; ?>">Add highlighted events</option>
                                    </select>
                                </span>
                                <span class="export-loading">
                                    <img src="<?php echo EXAMPAL_PATH; ?>/assets/img/Page-1.png"/> Loading
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    <div class="calendar-area">
        <?php 

            $calendar = new Calendar($_POST['exampal-plan-date'], get_option('exampal_plan_spreadsheet_array'), $_POST['plan']);

            echo $calendar->show();
        ?>
    </div>  
        <br/>
    </div>

    <?php
    $out = ob_get_clean();

    return $out;
}

add_shortcode('exampal_plan_calendar', 'exampal_plan_calendar_shortcode');