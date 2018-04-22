<?php

/**
 * Class create calendar
 */
class Calendar
{

    /********************* PROPERTY ********************/
    private $dayLabels = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");

    private $currentYear;

    private $currentMonth;

    private $currentDay;

    private $currentDate;

    private $startDay;

    private $daysInMonth;

    private $plan_arr;

    private $plan;


    /**
     * @param string $currentDay
     * @param array $plan_arr
     * @param int $plan
     */
    function __construct($currentDay, $plan_arr, $plan = 0) {
        $this->currentDay = date('j', strtotime($currentDay));
        $this->startDay = date('j', strtotime($currentDay));
        $this->currentMonth = date('m', strtotime($currentDay));
        $this->currentYear = date('Y', strtotime($currentDay));
        $this->plan = $plan - 1;
        $this->plan_arr = $this->getPlan($plan_arr);

    }

    /**
     * Show html calendar
     * @return string $content
     */
    public function show() {
        $this->daysInMonth = $this->daysInMonth($this->currentMonth, $this->currentYear);


        $content = '<table align="center" id="calendarcontenttbl">';
        $content .= '<tr id="customtblhead">';
        $content .= $this->createLabels();
        $content .= '</tr>';
        $weeksInCourse = $this->weeksInCourse();
        for ($i = 0; $i < $weeksInCourse; $i++) {
            $content .= '<tr>';
            if ($i == 0) {
                $this->start();
            }
            for ($j = 1; $j <= 7; $j++) {
                $content .= $this->showDay($i * 7 + $j);
            }
            $content .= '</tr>';
            $content .= '<tr>';
            for ($k = 0; $k < 7; $k++) {

                $content .= $this->getContent($k, $i);
            }
        }
        $content .= '</tr>';
        $content .= '</table>';
        $content .= '</div>';

        return $content;
    }

    /**
     * Creat array whith date and plun day
     * @return array $arrayPlan
     */
    public function getArray() {
        $this->daysInMonth = $this->daysInMonth($this->currentMonth, $this->currentYear);


        $arrayPlan = [];
        array_push($arrayPlan, $this->dayLabels);
        $weeksInCourse = $this->weeksInCourse();

        for ($i = 0; $i < $weeksInCourse; $i++) {
            if ($i == 0) {
                $this->start();
            }
            $arrDay = [];
            for ($j = 1; $j <= 7; $j++) {
                array_push($arrDay, strip_tags($this->showDay($i * 7 + $j)));
            }
            $arrDayContent = [];
            for ($k = 0; $k < 7; $k++) {

                array_push($arrDayContent, strip_tags($this->getContentArray($k, $i)));
            }
            array_push($arrayPlan, $arrDay);
            array_push($arrayPlan, $arrDayContent);
        }

        return $arrayPlan;
    }

    /**
     * Get array to import to google or outlook calendar
     * @return array
     */
    public function getArrayImport(){
        $this->daysInMonth = $this->daysInMonth($this->currentMonth, $this->currentYear);

        $arrayPlan = [];
        $weeksInCourse = $this->weeksInCourse();

        for ($i = 0; $i < $weeksInCourse; $i++) {
            if ($i == 0) {
                $this->start();
            }
            $arrDay = [];
            for ($j = 1; $j <= 7; $j++) {
                array_push($arrDay, strip_tags($this->showDayImport()));
            }
            $arrDayContent = [];
            for ($k = 0; $k < 7; $k++) {
                $currentDayContent = $this->getContentArray($k, $i);
                $currentDayContent = strip_tags($currentDayContent);
                array_push($arrDayContent, $currentDayContent);
            }
            if ($i == $weeksInCourse - 1) {
                foreach ($arrDayContent as $key => $value) {
                    if (empty($arrDayContent[$key])) {
                        unset($arrDayContent[$key]);
                        unset($arrDay[$key]);
                    }
                }
            }
            array_push($arrayPlan, $arrDay);
            array_push($arrayPlan, $arrDayContent);
        }

        return $arrayPlan;
    }

    /**
     * Get array of highlights to import to google or outlook calendar
     * @return array
     */
    public function getArrayHighlightsImport(){
        $this->daysInMonth = $this->daysInMonth($this->currentMonth, $this->currentYear);

        $arrayPlan = [];
        $weeksInCourse = $this->weeksInCourse();

        for ($i = 0; $i < $weeksInCourse; $i++) {
            if ($i == 0) {
                $this->start();
            }
            $arrDay = [];
            for ($j = 1; $j <= 7; $j++) {
                array_push($arrDay, strip_tags($this->showDayImport()));
            }
            $arrDayContent = [];
            for ($k = 0; $k < 7; $k++) {
                $currentEventArr = array();

                $currentDayContent = $this->getContentArray($k, $i);
                $arrContent = explode("\n", $currentDayContent);
                $arrHightlights = array_filter($arrContent, 'exampla_contains_bold');
                // preg_match('/\d\d:?\d\d\s?-\s?\d\d:?\d\d/', $test)
                if (!empty($arrHightlights)) {
                    foreach ($arrHightlights as $highlight_key => $value) {
                        $value = str_replace(':</span>', ': </span>', $value);
                        $variable = explode(': ', $value, 2);
                        foreach ($variable as $key => $dayRow) {
                            if (preg_match('/\d\d:?\d\d\s?-\s?\d\d:?\d\d/', $dayRow)) {
                                $currentEventArr['time'] = strip_tags($dayRow);
                            } else {
                                $currentEventArr['content'] = strip_tags($dayRow);
                            }
                        }
                        if (!isset($currentEventArr['time'])) {
                            $currentEventArr['time'] = '';
                            if (preg_match('/\d\d:?\d\d\s?-\s?\d\d:?\d\d/', $arrContent[$highlight_key - 1])) {
                                $currentEventArr['time'] = strip_tags($arrContent[$highlight_key - 1]);
                            } elseif (preg_match('/\d\d:?\d\d\s?-\s?\d\d:?\d\d/', $arrContent[$highlight_key - 2])) {
                                $currentEventArr['time'] = strip_tags($arrContent[$highlight_key - 2]);
                            }
                        }
                    }
                }
                array_push($arrDayContent, $currentEventArr);
            }
            if ($i == $weeksInCourse - 1) {
                foreach ($arrDayContent as $key => $value) {
                    if (empty($arrDayContent[$key])) {
                        unset($arrDayContent[$key]);
                        unset($arrDay[$key]);
                    }
                }
            }
            array_push($arrayPlan, $arrDay);
            array_push($arrayPlan, $arrDayContent);
        }

        return $arrayPlan;
    }

    /**
     * Get current plan
     * @param array $plan_arr
     * @return array
     */
    private function getPlan($plan_arr){
        $key = array_keys($plan_arr);
        return $plan_arr[$key[$this->plan]];
    }

    /**
     * Count weeks in current study plan
     * @return int
     */
    private function weeksInCourse(){
        $plan_arr = $this->plan_arr;
        $numOfweeks = (count($plan_arr) - 1) / 2;

        return $numOfweeks;
    }

    /**
     * Count days in month
     * @param string $month
     * @param string $year
     * @return string
     */
    private function daysInMonth($month, $year){
        $this->daysInMonth = date('t', strtotime($year . '-' . $month . '-01'));
        return $this->daysInMonth;
    }

    /**
     * Find first monday after pick date
     */
    private function start(){
        $day = date('N', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . $this->startDay));

        if ($day != 1 && $this->currentDay <= $this->startDay) {
            if ($this->startDay + 7 - $day + 1 > $this->daysInMonth) {
                $this->currentMonth++;

                if ($this->currentMonth > 12) {
                    $this->currentYear++;
                    $this->currentMonth = 1;
                }
                $this->startDay = 1;
                $this->currentDay = $this->startDay;


                while (date('N', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . $this->startDay)) != 1) {
                    $this->startDay++;
                    $this->currentDay = $this->startDay;

                }

            } else {
                $this->currentDay = $this->startDay + 7 - $day + 1;
            }

        }
    }

    /**
     * Creates label , day name
     * @return string
     */
    private function createLabels(){

        $content = '';
        foreach ($this->dayLabels as $index => $label) {
            if ($index == 6) {
                $title = 'end title';
            } else {
                $title = 'stat title';
            }
            $content .= '<td align="center" width="14%"><strong>' . $label . '</strong></td>';
        }
        return $content;
    }

    /**
     * Create day
     * @param int $cellNumber
     * @return string
     */
    private function showDay($cellNumber){

        if ($cellNumber % 7 == 1) {
            $title = 'start';
        } elseif ($cellNumber % 7 == 0) {
            $title = 'end';
        } else {
            $title = '';
        }

        $date = $this->getDay();

        $cell_content = (($date == 1) || ($cellNumber == 1)) ? date('F', strtotime($this->currentDate)) . ' ' . $date : $date;


        /*$cell_content = $this->currentYear . '-' . $this->currentMonth . '-' . $this->currentDay;*/

        return '<td id="li-' . $this->currentDate . '" align="center">' . $cell_content . '</td>';
    }

    /**
     * Get current day
     * @return int
     */
    private function getDay(){
        $cellContent = $this->currentDay;
        $this->moveNextDay();

        return $cellContent;
    }

    /**
     * Get current day (Import format)
     * @return int
     */
    private function showDayImport(){
        $cellContent = $this->currentMonth . '/' . $this->currentDay . '/' . $this->currentYear;
        $this->moveNextDay();

        return $cellContent;
    }

    /**
     * Move to next day
     * @return int
     */
    private function moveNextDay(){
        $this->currentDate = date('Y-m-d', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . $this->currentDay));

        if ($this->currentDay == $this->daysInMonth($this->currentMonth, $this->currentYear)) {
            if( $this->currentMonth == 12){
                $this->currentMonth = 1;
                $this->currentYear++;
            } else {
                $this->currentMonth++;
            }
            $this->currentDay = 1;
        } else {
            $this->currentDay++;
        }
    }


    /**
     * Get data to Array
     * @param int $day
     * @param int $week
     * @return string
     */
    private function getContentArray($day, $week){
        $week = $week * 2 + 2;
        $plan_arr = $this->plan_arr;
        return $plan_arr[$week][$day];
    }

    /**
     * Get conten to view calendar
     * @param int $day
     * @param int $week
     * @return string
     */
    private function getContent($day, $week){
        $week = $week * 2 + 2;
        $plan_arr = $this->plan_arr;
        $arrContent = explode("\n", $plan_arr[$week][$day]);
        $content = '<td>';
        foreach ($arrContent as $value) {
            $value = str_replace(':</span>', ': </span>', $value);
            $variable = explode(': ', $value, 2);
            foreach ($variable as $key => $span) {

                if (strpos($span, 'bold')) {
                    $content .= '<span class="red">' . strip_tags($span) . '</span>';
                } else {
                    if (!empty(strip_tags($span))) {
                        $content .= '<span>' . strip_tags($span) . '</span>';
                    }
                }


            }

        }
        $content .= '</td>';
        return $content;
    }


}

