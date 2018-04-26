<?php

// Generate ics file for export available by certain link
function exampal_calendar_export_ics(){

    if ( isset( $_GET[ 'calendar-export' ] ) && ($_GET[ 'calendar-export' ] == 'ics')) {

        $plan_date = (empty($_GET[ 'date' ])) ? date("F j, Y") : $_GET[ 'date' ];
        $plan_type = (empty($_GET[ 'plan' ])) ? 1 : ($_GET[ 'plan' ]);



        $eol = "\r\n";
        $ics = '';

        $ics .= "BEGIN:VCALENDAR" . $eol .
            "VERSION:2.0" . $eol .
            "CALSCALE:GREGORIAN" . $eol .
            "METHOD:PUBLISH" . $eol .
            "X-WR-CALNAME:GMAT Study Plan" . $eol .
            "X-PUBLISHED-TTL:PT2H" . $eol .
            "PRODID:-//hacksw/handcal//NONSGML v1.0//EN" . $eol;

        $calendar = new Calendar($plan_date, get_option('exampal_plan_spreadsheet_array'), $plan_type);

        $calArrays = $calendar->getArrayImport();


        $day = 1;
        for($i=0; $i < count($calArrays); $i+=2) {
            foreach ($calArrays[$i] as $key => $date){
                $date_end = new DateTime($date);
                $date_end->modify('+1 day');

                $ics .= "BEGIN:VEVENT" . $eol .
                    "DTSTAMP:" . dateToCal() . $eol .
                    "UID:exampalstudyplan" . $day . $eol .
                    "DTSTART;VALUE=DATE:" . dateToCalShort($date) . $eol .
                    "DTEND;VALUE=DATE:" . $date_end->format('Ymd') . $eol .
                    "SUMMARY:" . htmlspecialchars('Exampal Study Plan Day '.$day) . $eol .
                    "DESCRIPTION:" . htmlspecialchars(str_replace("\n", "\\n", $calArrays[$i+1][$key])) . $eol .
                    "X-ALT-DESC;FMTTYPE=text/html:" . htmlspecialchars(str_replace("\n", "<br />", $calArrays[$i+1][$key])) . $eol .
                    "SEQUENCE:0" . $eol.
                    "STATUS:CONFIRMED" . $eol.
                    "TRANSP:TRANSPARENT" . $eol.
                    "END:VEVENT" . $eol;

                $day++;
            }
        }

        $ics .= "END:VCALENDAR";

        $ics_filename='exampal-study-plan-' . time() . '.ics';

        // Set the headers
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $ics_filename);

        echo $ics;

        exit;
    }
}
add_action('wp_loaded', 'exampal_calendar_export_ics');

// Generate ics file for export available by certain link
function exampal_calendar_export_ics_highlighted(){

    if ( isset( $_GET[ 'calendar-export' ] ) && ($_GET[ 'calendar-export' ] == 'ics-highlighted')) {

        $plan_date = (empty($_GET[ 'date' ])) ? date("F j, Y") : $_GET[ 'date' ];
        $plan_type = (empty($_GET[ 'plan' ])) ? 1 : ($_GET[ 'plan' ]);



        $eol = "\r\n";
        $ics = '';

        $ics .= "BEGIN:VCALENDAR" . $eol .
            "VERSION:2.0" . $eol .
            "CALSCALE:GREGORIAN" . $eol .
            "METHOD:PUBLISH" . $eol .
            "X-WR-CALNAME:GMAT Study Plan" . $eol .
            "X-PUBLISHED-TTL:PT2H" . $eol .
            "PRODID:-//hacksw/handcal//NONSGML v1.0//EN" . $eol;

        $calendar = new Calendar($plan_date, get_option('exampal_plan_spreadsheet_array'), $plan_type);

        $calArrays = $calendar->getArrayHighlightsImport();


        $day = 1;
        for($i=0; $i < count($calArrays); $i+=2) {
            if (empty($calArrays[$i]) || empty($calArrays[$i+1])) continute;
            foreach ($calArrays[$i] as $key => $date){
                if (empty($date) || !isset($calArrays[$i+1][$key]['content']) || !isset($calArrays[$i+1][$key]['time']) || empty($calArrays[$i+1][$key]['time'])) continue;

                preg_match('/(\d\d:?\d\d)\s?-\s?(\d\d:?\d\d)/', $calArrays[$i+1][$key]['time'], $time_matches);
                $time_start = str_replace(array(':', ' '), '', $time_matches[1]);
                $time_end = str_replace(array(':', ' '), '', $time_matches[2]);

                $date_end = new DateTime($date);
                if ($time_start > $time_end) {
                    $date_end->modify('+1 day');
                }

                $ics .= "BEGIN:VEVENT" . $eol .
                    "DTSTAMP:" . dateToCal() . $eol .
                    "UID:exampalstudyplan" . $day . $eol .
                    "DTSTART;VALUE=DATE-TIME:" . dateToCal($date.' '.$time_start) . $eol .
                    "DTEND;VALUE=DATE-TIME:" . dateToCal($date_end->format('Ymd').' '.$time_end) . $eol .
                    "SUMMARY:" . htmlspecialchars(str_replace("\n", " ", $calArrays[$i+1][$key]['content'])) . $eol .
                    "DESCRIPTION:" . htmlspecialchars(str_replace("\n", "\\n", $calArrays[$i+1][$key]['content'])) . $eol .
                    "X-ALT-DESC;FMTTYPE=text/html:" . htmlspecialchars(str_replace("\n", "<br />", $calArrays[$i+1][$key]['content'])) . $eol .
                    "SEQUENCE:0" . $eol.
                    "STATUS:CONFIRMED" . $eol.
                    "TRANSP:TRANSPARENT" . $eol.
                    "END:VEVENT" . $eol;

                $day++;
            }
        }

        $ics .= "END:VCALENDAR";

        $ics_filename='exampal-study-plan-' . time() . '.ics';

        // Set the headers
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $ics_filename);

        echo $ics;

        exit;
    }
}
add_action('wp_loaded', 'exampal_calendar_export_ics_highlighted');

function dateToCal($date = '') {
    $date = (empty($date)) ? time() : strtotime($date);
    return date('Ymd\TGis\Z', $date);
}
function dateToCalShort($date = '') {
    $date = (empty($date)) ? time() : strtotime($date);
    return date('Ymd', $date);
}

/**
 * Awful addevent.com export. Avoid to use it. Currently not used
 * Create and send calendar to google or outlook
 * @return type
 */
function addevent_add_calendar(){

    if( isset( $_POST['exampal_add_calendar'] ) && wp_verify_nonce( $_POST['exampal_add_calendar'], 'exampal_add_calendar_nonce' ) && (isset($_POST['addtocalendar']) && !empty($_POST['addtocalendar']))) {

        if(empty($_POST['exampal-plan-date'])){
            $_POST['exampal-plan-date'] = date("F j, Y");
        }
        if(empty($_POST['plan'])){
            $_POST['plan'] = 1;
        }
        if(empty($_POST['addtocalendar'])){
            $_POST['addtocalendar'] == 'google';
        }
        $token = get_option('exampal_addevent_token');

        $result = wp_remote_get('https://www.addevent.com/api/v1/me/calendars/create/?token='.$token.'&title=Study-plan&description=Study-plan');
        $result = json_decode($result['body']);
        if (!empty($result) && isset($result->calendar) && isset($result->calendar->id) && !empty($result->calendar->id)) {
            echo '<h2 style="text-align: center; margin-top: 50px; font-family: \'Lato\',Arial,sans-serif;">'.__("Please, wait. Generating calendar data for export...", 'exampal').'</h2>';
            $calen_id = $result->calendar->id;
            $cal_key = $result->calendar->uniquekey;

            $calendar = new Calendar($_POST['exampal-plan-date'], get_option('exampal_plan_spreadsheet_array'), $_POST['plan']);

            $calArrays = $calendar->getArrayImport();


            $day = 1;
            for($i=0; $i < count($calArrays); $i+=2) {
                foreach ($calArrays[$i] as $key => $date){
                    wp_remote_get('https://www.addevent.com/api/v1/me/calendars/events/create/?token=' . $token . '&calendar_id=' . $calen_id.'&title=Exampal+Study+Plan+Day+'.$day.'&timezone=America/Los_Angeles&description=' . urlencode($calArrays[$i+1][$key]) . '&start_date=' . $date . '&end_date=' . $date . '&all_day_event=true');
                    $day++;
                }
            }

            echo '<h2 style="text-align: center; margin-top: 50px; font-family: \'Lato\',Arial,sans-serif;">'.__("Done. Redirecting to calendar service...", 'exampal').'</h2>';
            ?>


            <div style="opacity: 0;">
                <?php if($_POST['addtocalendar'] == 'google'){?>
                    <div id="google"><div title="Add to Google Calendar" class="addeventstc" data-direct="google" data-id="<?php echo $cal_key?>">Add to Google Calendar</div></div>
                <?php }elseif($_POST['addtocalendar'] == 'outlookcom'){ ?>
                    <div id="outlookcom"><div title="Add to Outlook Calendar" class="addeventstc" data-direct="outlookcom" data-id="<?php echo $cal_key?>">Add to Outlook Calendar</div></div>
                <?php }?>
            </div>

            <script type="text/javascript" src="https://addevent.com/libs/stc/1.0.2/stc.min.js"></script>

            <script type="text/javascript">
                function triggerClick (selector) {
                    var event = new Event('click');
                    selector.dispatchEvent(event);
                }

                if (window.addEventListener) {
                    // Capture click on button
                    window.addeventasync = function () {
                        addeventstc.register('button-click', function (obj) {

                        });
                    };
                    window.addEventListener("DOMContentLoaded", function () {

                        setTimeout(function () {

                            <?php if($_POST['addtocalendar'] == 'google'){?>
                            document.onready = triggerClick(document.querySelector('#google .addeventstc'));
                            <?php }elseif($_POST['addtocalendar'] == 'outlookcom'){ ?>
                            document.onready = triggerClick(document.querySelector('#outlookcom .addeventstc'));
                            <?php }?>
                        }, 100);
                    });
                }
            </script>

            <?php
        } else {
            echo '<h2 style="text-align: center; margin-top: 50px; font-family: \'Lato\',Arial,sans-serif;">'.__("Error. Can't create table.", 'exampal').'</h2>';
        }

        exit;
    }
}
/*add_action('wp', 'addevent_add_calendar');*/