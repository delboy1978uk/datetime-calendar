<?php
/**
 * User: delboy1978uk
 * Date: 20/05/2013
 * Time: 19:25
 */

class Del_Calendar
{
    /** @var null|string */
    protected $_start_of_week;

    /** @var array */
    protected $_days_of_week;

    /** @var DateTime */
    protected $_date;

    /** @var array  */
    protected $_content;

    /** @var array  */
    protected $_links;

    /** @var string  */
    protected $_default_link;

    /** @var string  */
    protected $_past_links;

    /** @var string  */
    protected $_calendar_url;

    /**
     * @param string $calendar_url the url (gets appended with '/year/:year/month/:month')
     * @param string $start_of_week Mon - Fri, pick your day
     * @param bool $past_links whether days before today contain custom content
     */
    public function __construct($calendar_url, $start_of_week = null,$past_links = false)
    {
        $this->_calendar_url = $calendar_url;
        if(!$start_of_week)
        {
            $start_of_week = 'Mon';
        }
        $this->_start_of_week = $start_of_week;
        $this->_date = new DateTime();
        $this->_date->modify('first day of this month');
        $this->_past_links = $past_links;
        $this->_content = array();
        $this->_links = array();
    }

    public function setDate(DateTime $date)
    {
        $this->_date = $date;
    }

    public function setDefaultLink($url)
    {
        $this->_default_link = $url;
    }


    private function getPrevLink()
    {
        $year = $this->_date->format('Y');
        $month = $this->_date->format('m');
        if($month == 1){$month = 12; $year = $year - 1;}
        else{$month = $month - 1;}
        return '<a style="margin-top: 8px;" class="btn btn-large" href="'.$this->_calendar_url.'/year/'.$year.'/month/'.$month.'">Prev</a>';
    }

    private function getNextLink()
    {
        $year = $this->_date->format('Y');
        $month = $this->_date->format('m');
        if($month == 12){$month = 1; $year = $year + 1;}
        else{$month = $month + 1;}
        return '<a style="margin-top: 8px;" class="btn btn-large" href="'.$this->_calendar_url.'/year/'.$year.'/month/'.$month.'">Next</a>';
    }

    private function getHeader()
    {
        return '<h3>'.$this->_date->format('M').' '.$this->_date->format('Y').'</h3>';
    }









    public function renderCalendar()
    {
        $html = '<div class="calendar">'
                    .'<div class="navbar">'
                        .'<div class="navbar-inner">'
                            .'<div class="pc33 tl pull-left prevnext">'.$this->getPrevLink().'</div>'
                            .'<div class="pc33 tc pull-left">'.$this->getHeader().'</div>'
                            .'<div class="pc33 tr pull-left prevnext">'.$this->getNextLink().'</div>'
                        .' </div>'
                    .' </div>'
                    .'<div class="well calendardays">'
                        .$this->getContent()
                    .'</div>'
                .'</div>';
        return $html;
    }










    private function getContent()
    {
        $html = '<table class="pc100">'
                    .'<thead>'
                        .'<tr>'
                            .$this->getDaysOfWeek()
                        .'</tr>'
                    .'</thead>'
                    .'<tbody>'
                        .$this->getDayBoxes()
                    .'</tbody>'
                .'</table>';
        return $html;
    }









    private function getDayBoxes()
    {
        $html = '';
        $today = new DateTime();
        $tmp_date = clone ($this->_date);

        //up to six rows in a month view calendar
        for($y = 1; $y <= 6; $y ++)
        {
            // render the row if its still the correct month
            if($tmp_date->format('m') == $this->_date->format('m'))
            {
                // 7 columns for days of week
                $html .='<tr>';
                for($x = 1; $x <= 7; $x ++)
                {

                    $html .= '<td class="tc calendarday';

                    //figure out starting box
                    switch($this->_date->format('D'))
                    {
                        case $this->_days_of_week[0]:
                            $z = 1;
                            break;
                        case $this->_days_of_week[1]:
                            $z = 2;
                            break;
                        case $this->_days_of_week[2]:
                            $z = 3;
                            break;
                        case $this->_days_of_week[3]:
                            $z = 4;
                            break;
                        case $this->_days_of_week[4]:
                            $z = 5;
                            break;
                        case $this->_days_of_week[5]:
                            $z = 6;
                            break;
                        case $this->_days_of_week[6]:
                            $z = 7;
                            break;
                    }
                    // if the 1st of the month is ahead of the box on the first row grey it out
                    if($z > $x && $y == 1)
                    {
                        $html .= ' greyed ">&nbsp;</td>';
                    }
                    //same with last row, end of month greying out
                    elseif ($y > 4 && $tmp_date->format('m') > $this->_date->format('m'))
                    {
                        $html .= ' greyed ">&nbsp;</td>';
                    }
                    //start of new year grey out
                    elseif($y > 4 && $tmp_date->format('m') == 1 && $this->_date->format('m') == 12)
                    {
                        $html .= ' greyed ">&nbsp;</td>';
                    }
                    //days of this month already past
                    elseif($tmp_date->format('d') < $today->format('d') && ($tmp_date->format('m') == $today->format('m') && $tmp_date->format('Y') == $today->format('Y')) && $this->_past_links == false)
                    {
                        $html .= ' past ">'.$tmp_date->format('d').'</td>';
                        $tmp_date->add(new DateInterval('P1D'));
                    }
                    else
                    {
                        // days left in this month

                        //is it today?
                        if ($tmp_date->format('d') == $today->format('d') && $tmp_date->format('m') == $today->format('m') && $tmp_date->format('Y') == $today->format('Y'))
                        {
                            $html .=' bluebox ';
                        }

                        $html .=' active">';
                        $html .= $this->getBoxContent($tmp_date->format('d'));
                        $html .= '</td>';
                        $tmp_date->add(new DateInterval('P1D'));
                    }
                }
                $html .='</tr>';
            }
        }
        return $html;
    }








    private function getDaysOfWeek()
    {
        $html = '';
        $this->_days_of_week = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
        $offset = 0;
        if('Mon' != $this->_start_of_week)
        {
            foreach($this->_days_of_week as $day)
            {
                if($day == $this->_start_of_week)
                {
                    break;
                }
                else
                {
                    array_shift($this->_days_of_week);
                    array_push($this->_days_of_week,$day);
                }
            }
        }
        for($x = $offset; $x <= 6; $x ++)
        {
            $html .= '<th>'
                        .$this->_days_of_week[$x]
                    .'</th>';
        }
        return $html;
    }









    public function setContent($day,$content)
    {
        if(!is_numeric($day) || ($day < 1 || $day > 31))
        {
            return false;
        }
        $this->_content[$day] = $content;
    }









    /**
     * Use :day :month :year in your URL, these will be string replaced.
     * @param int $day
     * @param string $url
     * @return bool
     */
    public function setLink($day,$url)
    {
        if(!is_numeric($day) || ($day < 1 || $day > 31))
        {
            return false;
        }
        $url = str_replace(':day',$day,$url);
        $url = str_replace(':month',$this->_date->format('m'),$url);
        $url = str_replace(':day',$this->_date->format('Y'),$url);
        $this->_links[$day] = $url;
    }




    public function getLink($day,$content)
    {
        if(isset($this->_links[$day]))
        {
            $link = str_replace(':year',$this->_date->format('Y'),$this->_links[$day]);
            $link = str_replace(':month',$this->_date->format('m'),$link);
            $link = str_replace(':day',$day,$link);
            $html = '<a href="'.$link.'">';
        }
        else
        {
            $link = str_replace(':year',$this->_date->format('Y'),$this->_default_link);
            $link = str_replace(':month',$this->_date->format('m'),$link);
            $link = str_replace(':day',$day,$link);
            $html = '<a href="'.$link.'">';
        }
        $html .= $content.'</a>';
        return $html;
    }



    public function getBoxContent($day)
    {
        if(isset($this->_content[$day]))
        {
            $content = $this->_content[$day];
        }
        else
        {
            $content = str_pad($day,2,'0',STR_PAD_LEFT);
        }
        return $this->getLink($day,$content);
    }
}