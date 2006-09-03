<?php /* CLASSES $Id$ */
/**
* @package dotproject
* @subpackage utilites
*/

require_once( $AppUI->getLibraryClass( 'PEAR/Date' ) );

define( 'FMT_DATEISO', '%Y%m%dT%H%M%S' );
define( 'FMT_DATELDAP', '%Y%m%d%H%M%SZ' );
define( 'FMT_DATETIME_MYSQL', '%Y-%m-%d %H:%M:%S' );
define( 'FMT_DATERFC822', '%a, %d %b %Y %H:%M:%S' );
define( 'FMT_TIMESTAMP', '%Y%m%d%H%M%S' );
define( 'FMT_TIMESTAMP_DATE', '%Y%m%d' );
define( 'FMT_TIMESTAMP_TIME', '%H%M%S' );
define( 'FMT_UNIX', '3' );
define( 'WDAY_SUNDAY',    0 );
define( 'WDAY_MONDAY',    1 );
define( 'WDAY_TUESDAY',   2 );
define( 'WDAY_WEDNESDAY',  3 );
define( 'WDAY_THURSDAY',  4 );
define( 'WDAY_FRIDAY',    5 );
define( 'WDAY_SATURDAY',  6 );
define( 'SEC_MINUTE',    60 );
define( 'SEC_HOUR',    3600 );
define( 'SEC_DAY',    86400 );

/**
* dotProject implementation of the Pear Date class
*
* This provides customised extensions to the Date class to leave the
* Date package as 'pure' as possible
*/
class CDate extends Date {

/**
* Overloaded compare method
*
* The convertTZ calls are time intensive calls.  When a compare call is
* made in a recussive loop the lag can be significant.
*/
    function compare($d1, $d2, $convertTZ=false)
    {
                if ($convertTZ) {
                        $d1->convertTZ(new Date_TimeZone('UTC'));
                        $d2->convertTZ(new Date_TimeZone('UTC'));
                }
        $days1 = Date_Calc::dateToDays($d1->day, $d1->month, $d1->year);
        $days2 = Date_Calc::dateToDays($d2->day, $d2->month, $d2->year);
        if($days1 < $days2) return -1;
        if($days1 > $days2) return 1;
        if($d1->hour < $d2->hour) return -1;
        if($d1->hour > $d2->hour) return 1;
        if($d1->minute < $d2->minute) return -1;
        if($d1->minute > $d2->minute) return 1;
        if($d1->second < $d2->second) return -1;
        if($d1->second > $d2->second) return 1;
        return 0;
    }


/**
* Adds (+/-) a number of days to the current date.
* @param int Positive or negative number of days
* @author J. Christopher Pereira <kripper@users.sf.net>
*/
        function addDays( $n ) {
                 $timeStamp = $this->getTime();
                 $oldHour = $this->getHour();
                 $this->setDate( $timeStamp + SEC_DAY * ceil($n), DATE_FORMAT_UNIXTIME);

                 if(($oldHour - $this->getHour()) || !is_int($n)) {
                     $timeStamp += ($oldHour - $this->getHour()) * SEC_HOUR;
                     $this->setDate( $timeStamp + SEC_DAY * $n, DATE_FORMAT_UNIXTIME);
                  }
        }

/**
* Adds (+/-) a number of months to the current date.
* @param int Positive or negative number of months
* @author Andrew Eddie <eddieajau@users.sourceforge.net>
*/
        function addMonths( $n ) {
                $an = abs( $n );
                $years = floor( $an / 12 );
                $months = $an % 12;

                if ($n < 0) {
                        $this->year -= $years;
                        $this->month -= $months;
                        if ($this->month < 1) {
                                $this->year--;
                                $this->month = 12 + $this->month;
                        }
                } else {
                        $this->year += $years;
                        $this->month += $months;
                        if ($this->month > 12) {
                                $this->year++;
                                $this->month -= 12;
                        }
                }
        }

/**
* New method to get the difference in days the stored date
* @param Date The date to compare to
* @author Andrew Eddie <eddieajau@users.sourceforge.net>
*/
        function dateDiff( $when ) {
                return Date_calc::dateDiff(
                        $this->getDay(), $this->getMonth(), $this->getYear(),
                        $when->getDay(), $when->getMonth(), $when->getYear()
                );
        }

/**
* New method that sets hour, minute and second in a single call
* @param int hour
* @param int minute
* @param int second
* @author Andrew Eddie <eddieajau@users.sourceforge.net>
*/
        function setTime( $h=0, $m=0, $s=0 ) {
                $this->setHour( $h );
                $this->setMinute( $m );
                $this->setSecond( $s );
        }

        function isWorkingDay(){
                global $AppUI;

                $working_days = dPgetConfig("cal_working_days");
                if(is_null($working_days)){
                        $working_days = array('1','2','3','4','5');
                } else {
                        $working_days = explode(",", $working_days);
                }

                return in_array($this->getDayOfWeek(), $working_days);
        }

        function getAMPM() {
                if ( $this->getHour() > 11 ) {
                        return "pm";
                } else {
                        return "am";
                }
        }


function workingDaysInSpan($e){
                global $AppUI;
		
		// assume start is before end and set a default signum for the duration	
		$sgn = 1;

		// check whether start before end, interchange otherwise
		if ($e->before($this)) {
			// duration is negative, set signum appropriately
			$sgn = -1;
		}    
		
		$wd = 0;
		$days = $e->dateDiff($this);
		$start = $this;

		for ( $i = 0 ; $i <= $days ; $i++ ){
		        if ( $start->isWorkingDay())
		        	$wd++;
			$start->addDays(1 * $sgn);
		}

                return $wd;
        }


	/* Return date obj for the end of the next working day
	** @param	bool	Determine whether to set time to start of day or preserve the time of the given object
	*/ 
        function next_working_day( $preserveHours = false ) {
                global $AppUI;
		$do = $this;
                $end = intval(dPgetConfig('cal_day_end'));
                $start = intval(dPgetConfig('cal_day_start'));
                while ( ! $this->isWorkingDay() || $this->getHour() >= $end ) {
                        $this->addDays(1);
                        $this->setTime($start, '0', '0');
                }
		if ($preserveHours)
			$this->setTime($do->getHour(), '0', '0');

                return $this;
        }


        /* Return date obj for the end of the previous working day
	** @param	bool	Determine whether to set time to end of day or preserve the time of the given object
	*/ 
        function prev_working_day( $preserveHours = false  ) {
                global $AppUI;
		$do = $this;
                $end = intval(dPgetConfig('cal_day_end'));
                $start = intval(dPgetConfig('cal_day_start'));
                while ( ! $this->isWorkingDay() || ( $this->getHour() < $start ) ||
                              ( $this->getHour() == $start && $this->getMinute() == '0' ) ) {
                        $this->addDays(-1);
			$this->setTime($end, '0', '0');
                }
		if ($preserveHours)
			$this->setTime($do->getHour(), '0', '0');

                return $this;
        }


	/* Calculating _robustly_ a date from a date and duration given
	** Works in both directions: forwards/prospective and backwards/retrospective
	** Respects non-working days
	** @param	int	duration
	** @param	int	durationType; 1 = hour; 24 = day;
	** @return	obj	Shifted DateObj
	*/ 

	function addDuration( $duration = '8', $durationType ='1') {
		// using a sgn function lets us easily cover 
		// prospective and retrospective calcs at the same time

		// get signum of the duration
		$sgn = dPsgn($duration);
		
		// make duration positive
		$duration = abs($duration);

		// in case the duration type is 24 resp. full days
		// we're finished very quickly
		if ($durationType == '24') {
			$d->addDays($duration * $sgn);
			return $this->prev_working_day(true);
		}

		// durationType is 1 hour
			
		// get dP time constants
	      	$cal_day_start = intval(dPgetConfig( 'cal_day_start' ));
	        $cal_day_end = intval(dPgetConfig( 'cal_day_end' ));
	        $dwh = intval(dPgetConfig( 'daily_working_hours' ));

	// proceeding the actual (first) day

		// move to the next working day if the first day is a non-working day
		 ($sgn > 0) ? $this->next_working_day() : $this->prev_working_day();

	
		$firstDay = ($sgn > 0) ? $cal_day_end - $this->hour : $this->hour - $cal_day_start;

		/*
		** if we're later than cal_end_day or sooner than cal_start_day
		** just move by one day without subtracting any time from duration 
		*/
		if ($firstDay < 0)
			$firstDay = 0;

		if ($duration < $firstDay) {
			($sgn > 0) ? $this->setHour($this->hour+$duration) : $this->setHour($this->hour-$duration);
			return $this;
		}

		$firstAdj = min($dwh, $firstDay);


		$this->addDays(1 * $sgn);
		($sgn > 0) ? $this->next_working_day() : $this->prev_working_day();
		$duration -= $firstAdj;

	// end of proceeding the first day
			
		// calc the remaining time
		$hoursRemaining = ($duration > $dwh) ? ($duration % $dwh) : $duration;
                $full_working_days = round(($duration - $hoursRemaining) / $dwh);


	// proceed the last day

		// we prefer wed 16:00 over thu 08:00 as end date :)
		if ($hoursRemaining == 0){
			$full_working_days--;
			($sgn > 0) ? $this->setHour($cal_day_start+$dwh) : $this->setHour($cal_day_end-$dwh);
		} else
			($sgn > 0) ? $this->setHour($cal_day_start+$hoursRemaining) : $this->setHour($cal_day_end-$hoursRemaining);
	//end of proceeding the last day


	// proceeding the fulldays
		// Full days
		for ( $i = 0 ; $i < $full_working_days ; $i++ ) {
		        $this->addDays(1 * $sgn);
		        if ( !$this->isWorkingDay() )
		                $full_working_days++;
		}
	//end of proceeding the fulldays

		return $this;
	}


	/* Calculating _robustly_ the working duration between two dates
	** Works in both directions: forwards/prospective and backwards/retrospective
	** Respects non-working days
	** @param	obj	DateObject
	** @return	int	working duration
	*/ 
	function calcDurationDiffToDate($e) {
		// get dP time constants
	      	$cal_day_start = intval(dPgetConfig( 'cal_day_start' ));
	        $cal_day_end = intval(dPgetConfig( 'cal_day_end' ));
	        $dwh = intval(dPgetConfig( 'daily_working_hours' ));
		
		// assume start is before end and set a default signum for the duration	
		$sgn = 1;

		// check whether start before end, interchange otherwise
		if ($e->before($this)) {
			// duration is negative, set signum appropriately
			$sgn = -1;

			$dummy = $this;
			$this=$e;	
			$e = $dummy;
		}    

		$days = $e->dateDiff($this);

		if($days == 0)
			return min($dwh, abs($e->hour - $this->hour))*$sgn;

            	$duration = 0;
		
		// take into account the first day if it is a working day!
		$duration += $this->isWorkingDay() ? min($dwh, abs($cal_day_end - $this->hour)) : 0;
		$this->addDays(1);

		// calc workingdays between start and end
		for ($i=1; $i < $days; $i++) {
			$duration += $this->isWorkingDay() ? $dwh : 0;
			$this->addDays(1);

		}
		// take into account the last day in span if it is a working day!
		$duration += $this->isWorkingDay() ? min($dwh, abs($e->hour - $cal_day_start)) : 0;

		return $duration*$sgn;
	}	
}
?>
