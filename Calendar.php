<?php
/**
 * User: delboy1978uk
 * Date: 20/05/2013
 * Time: 19:25
 */

class Del_Calendar
{
    protected $_start_of_week;
    protected $_days_of_week;
    /** @var DateTime */
    protected $_date;

    /**
     * @param string $start_of_week Mon - Fri, pick your day
     */
    public function __construct($start_of_week = null)
    {
        if(!$start_of_week)
        {
            $start_of_week = 'Mon';
        }
        $this->_start_of_week = $start_of_week;
        $this->_date = new DateTime();
        $this->_date->modify('first day of this month');
    }

    public function setDate(DateTime $date)
    {
        $this->_date = $date;
    }


    private function getPrevLink()
    {
        return '<a style="margin-top: 8px;" class="btn btn-large" href="/blah">Prev</a>';
    }

    private function getNextLink()
    {
        return '<a style="margin-top: 8px;" class="btn btn-large" href="/blah">Next</a>';
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
        for($y = 1; $y <= 6; $y ++)
        {
            // don't do the row if its the start of a new month
            if($tmp_date->format('m') == $this->_date->format('m'))
            {
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
                    //if the 1st is ahead of the box on the first row grey it out
                    if($z > $x && $y == 1)
                    {
                        $html .= ' greyed ">&nbsp;</td>';
                    }
                    //end of month greying out
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
                    elseif($tmp_date->format('d') < $today->format('d') && ($tmp_date->format('m') == $today->format('m') && $tmp_date->format('Y') == $today->format('Y')))
                    {
                        $html .= ' past ">'.$tmp_date->format('d').'</td>';
                        $tmp_date->add(new DateInterval('P1D'));
                    }
                    else
                    {
                        //is it today?
                        if ($tmp_date->format('d') == $today->format('d') && $tmp_date->format('m') == $today->format('m') && $tmp_date->format('Y') == $today->format('Y'))
                        {
                            $html .=' bluebox ';
                        }

                        if(1 > 0)
                        {
                            $html .=' active"><a href="/blahblahlinkhere//year/'.$this->_date->format('Y').'/month/'.$this->_date->format('m').'/day/'.$tmp_date->format('d').'">';
                        }
                        else
                        {
                            $html .=' active nofixtures"><a href="/blahblahlinkhere/year/'.$this->_date->format('Y').'/month/'.$this->_date->format('m').'/day/'.$tmp_date->format('d').'">';
                        }

                        $html .= $tmp_date->format('d').'</a></td>';
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
        if($this->_date->format('D') != $this->_start_of_week)
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
}