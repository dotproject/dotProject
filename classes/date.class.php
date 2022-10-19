<?php /* CLASSES $Id$ */
/**
* @package dotproject
* @subpackage utilites
*/
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
if (!empty($AppUI->user_locale)) {
  @include_once(DP_BASE_DIR.'/locales/'.$AppUI->user_locale.'/locales.php');  // LOCALE_FIRST_DAY gets defined here (gwyneth 20210419)
  @include_once(DP_BASE_DIR.'/locales/core.php');
  setlocale(LC_TIME, $AppUI->user_locale);
  define('DATE_CALC_BEGIN_WEEKDAY', LOCALE_FIRST_DAY);
} else {
  define('DATE_CALC_BEGIN_WEEKDAY', 1);  // reasonable default
}
require_once $AppUI->getLibraryClass('PEAR/Date');

define('FMT_DATEISO', '%Y%m%dT%H%M%S');
define('FMT_DATELDAP', '%Y%m%d%H%M%SZ');
define('FMT_DATETIME_MYSQL', '%Y-%m-%d %H:%M:%S');
define('FMT_DATETIME_HTML5', '%Y-%m-%dT%H:%M');
define('FMT_DATERFC822', '%a, %d %b %Y %H:%M:%S');
define('FMT_DATE_HTML5', '%Y-%m-%d');
define('FMT_TIMESTAMP', '%Y%m%d%H%M%S');
define('FMT_TIMESTAMP_DATE', '%Y%m%d');
define('FMT_TIMESTAMP_TIME', '%H%M%S');
define('FMT_UNIX', '3');
define('WDAY_SUNDAY',	  0);
define('WDAY_MONDAY',	  1);
define('WDAY_TUESDAY',	  2);
define('WDAY_WEDNESDAY', 3);
define('WDAY_THURSDAY',  4);
define('WDAY_FRIDAY',	  5);
define('WDAY_SATURDAY',  6);
define('SEC_MINUTE',	 60);
define('SEC_HOUR',	   3600);
define('SEC_DAY',	  86400);

/**
* dotProject implementation of the Pear Date class
*
* This provides customised extensions to the Date class to leave the
* Date package as 'pure' as possible
*/
class CDate extends Date {

  // deprecated constructor
	function CDate($date = null, $format = null)
	{
		if ($format == null && !is_object($date) && strlen($date) == 12 && strpos($date, '-') === false && strpos($date, '/') === false) {
			$date = $date.'00';
		} else if ($format != null && !is_object($date)) {
			$dateobj = new Date();
			$dateobj->setDate($date, $format);
			$date = $dateobj;
		}

		return parent::Date($date);
	}

  /**
   * Modern-style constructor.
   *
   * @param mixed $date optional - date/time to initialize
   * @param string $format to apply to the date
   * @return object A Date object created by the PEAR Date class
   */
  function __constructor($date = null, $format = null) {
    return self::CDate($date, $format);
  }


/**
* extend PEAR Date's format() meet to translation needs
*
* @param $format mixed
* @return $format mixed
*/
	function format($format) {
		global $AppUI;
		$AppUI->setBaseLocale();
		$output = parent::format($format);
		setlocale(LC_ALL, $AppUI->user_lang);
		return $output;
	}

	function after($when)
	{
		if (!is_object($when)) {
			$when = new CDate($when);
		}
		return parent::after($when);
	}

/**
* Overloaded compare method
*
* The convertTZ calls are time intensive calls.	 When a compare call is
* made in a recussive loop the lag can be significant.
*
* @param object $d1 First date to compare
* @param object $d2 Second date to compare
* @param boolean $convertTZ Deal with timezone conversion or not
* @return int Returns (-1 | 0 | 1) if first date is lower than; equal; or greater than second date
*/
	static function compare($d1, $d2, $convertTZ=false)
	{
		if (!is_object($d1)) {
			$d1 = new CDate($d1);
		} else if (!is_object($d2)) {
			$d2 = new CDate($d2);
		}
		if ($convertTZ) {
			$d1->convertTZ(new Date_TimeZone('UTC'));
			$d2->convertTZ(new Date_TimeZone('UTC'));
		}
		$days1 = Date_Calc::dateToDays($d1->day, $d1->month, $d1->year);
		$days2 = Date_Calc::dateToDays($d2->day, $d2->month, $d2->year);

		$comp_value = 0;
		if ($days1 - $days2) {
			$comp_value = $days1 - $days2;
		} else if ($d1->hour - $d2->hour) {
			$comp_value = dPsgn($d1->hour - $d2->hour);
		} else if ($d1->minute - $d2->minute) {
			$comp_value = dPsgn($d1->minute - $d2->minute);
		} else if ($d1->second - $d2->second) {
			$comp_value = dPsgn($d1->second - $d2->second);
		}
		return dPsgn($comp_value);
	}

/**
* Adds (+/-) a number of days to the current date.
* @param int Positive or negative number of days
* @author J. Christopher Pereira <kripper@users.sf.net>
*/
	function addDays($n) {
		$timeStamp = $this->getTime();
		$oldHour = $this->getHour();
		$this->setDate($timeStamp + SEC_DAY * ceil($n), DATE_FORMAT_UNIXTIME);

		if (($oldHour - $this->getHour()) || !is_int($n)) {
			$timeStamp += ($oldHour - $this->getHour()) * SEC_HOUR;
			$this->setDate($timeStamp + SEC_DAY * $n, DATE_FORMAT_UNIXTIME);
		}
	}

/**
* Adds (+/-) a number of months to the current date.
* @param int Positive or negative number of months
* @author Andrew Eddie <eddieajau@users.sourceforge.net>
*/
	function addMonths($n) {
		$an = abs($n);
		$years = floor($an / 12);
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
	function dateDiff($when, $ignoretime = false) {
		return Date_calc::dateDiff($this->getDay(), $this->getMonth(), $this->getYear(),
								   $when->getDay(), $when->getMonth(), $when->getYear());
	}

/**
* New method that sets hour, minute and second in a single call
* @param int hour
* @param int minute
* @param int second
* @author Andrew Eddie <eddieajau@users.sourceforge.net>
*/
	function setTime($h=0, $m=0, $s=0) {
		$this->setHour($h);
		$this->setMinute($m);
		$this->setSecond($s);
	}

	function isWorkingDay() {
		global $AppUI;

		$working_days = dPgetConfig("cal_working_days");
		$working_days = ((is_null($working_days)) ? array('1','2','3','4','5') : explode(",", $working_days));
		return in_array($this->getDayOfWeek(), $working_days);
	}

	function getAMPM() {
		return (($this->getHour() > 11) ? 'pm' : 'am');
	}

	/* Return date obj for the end of the next working day
	 ** @param	bool	Determine whether to set time to start of day or preserve the time of the given object
	 */
	function next_working_day($preserveHours = false) {
		global $AppUI;
		$do = $this;
		$end = intval(dPgetConfig('cal_day_end'));
		$start = intval(dPgetConfig('cal_day_start'));
		while (! $this->isWorkingDay() || $this->getHour() > $end ||
				($preserveHours == false && $this->getHour() == $end && $this->getMinute() == '0')) {
			$this->addDays(1);
			$this->setTime($start, '0', '0');
		}

		if ($preserveHours) {
			$this->setTime($do->getHour(), '0', '0');
		}

		return $this;
	}


	/* Return date obj for the end of the previous working day
	 ** @param	bool	Determine whether to set time to end of day or preserve the time of the given object
	 */
	function prev_working_day($preserveHours = false) {
		global $AppUI;
		$do = $this;
		$end = intval(dPgetConfig('cal_day_end'));
		$start = intval(dPgetConfig('cal_day_start'));
		while (! $this->isWorkingDay() || ($this->getHour() < $start) ||
				($this->getHour() == $start && $this->getMinute() == '0')) {
			$this->addDays(-1);
			$this->setTime($end, '0', '0');
		}
		if ($preserveHours) {
			$this->setTime($do->getHour(), '0', '0');
		}

		return $this;
	}


	/* Calculating _robustly_ a date from a given date and duration
	 ** Works in both directions: forwards/prospective and backwards/retrospective
	 ** Respects non-working days
	 ** @param	int	duration	(positive = forward, negative = backward)
	 ** @param	int	durationType; 1 = hour; 24 = day;
	 ** @return	obj	Shifted DateObj
	 */

	function addDuration($duration = '8', $durationType ='1') {
		// using a sgn function lets us easily cover
		// prospective and retrospective calcs at the same time

		// get signum of the duration
		$sgn = dPsgn($duration);

		// make duration positive
		$duration = abs($duration);

		if ($durationType == '24') { // duration type is 24, full days, we're finished very quickly
			$full_working_days = $duration;
		} else if ($durationType == '1') { // durationType is 1 hour
		// get dP time constants
			$cal_day_start = intval(dPgetConfig('cal_day_start'));
			$cal_day_end = intval(dPgetConfig('cal_day_end'));
			$dwh = intval(dPgetConfig('daily_working_hours'));

			// move to the next working day if the first day is a non-working day
			($sgn > 0) ? $this->next_working_day() : $this->prev_working_day();

			// calculate the hours spent on the first day
			$firstDay = ($sgn > 0) ? min($cal_day_end - $this->hour, $dwh) : min($this->hour - $cal_day_start, $dwh);

			/*
			 ** Catch some possible inconsistencies:
			 ** If we're later than cal_end_day or sooner than cal_start_day then we don't need to
			 ** subtract any time from duration. The difference is greater than the # of daily working hours
			 */
			if ($firstDay < 0) {
				$firstDay = 0;
			}
			// Intraday additions are handled easily by just changing the hour value
			if ($duration <= $firstDay) {
				($sgn > 0) ? $this->setHour($this->hour+$duration) : $this->setHour($this->hour-$duration);
				return $this;
			}

			// the effective first day hours value
			$firstAdj = min($dwh, $firstDay);

			// subtract the first day hours from the total duration
			$duration -= $firstAdj;

			// we've already processed the first day; move by one day!
			$this->addDays(1 * $sgn);

			// make sure that we didn't move to a non-working day
			($sgn > 0) ? $this->next_working_day() : $this->prev_working_day();

			// end of proceeding the first day

			// calc the remaining time and the full working days part of this residual
			$hoursRemaining = ($duration > $dwh) ? ($duration % $dwh) : $duration;
			$full_working_days = round(($duration - $hoursRemaining) / $dwh);

			// (proceed the full days later)

			// proceed the last day now

			// we prefer wed 16:00 over thu 08:00 as end date :)
			if ($hoursRemaining == 0 && $full_working_days > 0) {
				$full_working_days--;
				($sgn > 0) ? $this->setHour($cal_day_start+$dwh) : $this->setHour($cal_day_end-$dwh);
			} else {
				($sgn > 0) ? $this->setHour($cal_day_start+$hoursRemaining) : $this->setHour($cal_day_end-$hoursRemaining);
			}
			//end of proceeding the last day
		}

		// proceeding the fulldays finally which is easy
		// Full days
		for ($i = 0 ; $i < $full_working_days ; $i++) {
			$this->addDays(1 * $sgn);
			if (!$this->isWorkingDay()) {
				// just 'ignore' this non-working day
				$full_working_days++;
			}
		}
		//end of proceeding the fulldays

		return $this->next_working_day();
	}


	/* Calculating _robustly_ the working duration between two dates
	 **
	 ** Works in both directions: forwards/prospective and backwards/retrospective
	 ** Respects non-working days
	 **
	 **
	 ** @param	obj	DateObject	may be viewed as end date
	 ** @return	int							working duration in hours
	 */
	function calcDuration($e) {

		// since one will alter the date ($this) one better copies it to a new instance
		$s = new CDate();
		$s->copy($this);

		// get dP time constants
		$cal_day_start = intval(dPgetConfig('cal_day_start'));
		$cal_day_end = intval(dPgetConfig('cal_day_end'));
		$dwh = intval(dPgetConfig('daily_working_hours'));

		// assume start is before end and set a default signum for the duration
		$sgn = 1;

		// check whether start before end, interchange otherwise
		if ($e->before($s)) {
			// calculated duration must be negative, set signum appropriately
			$sgn = -1;

			$dummy = $s;
			$s->copy($e);
			$e = $dummy;
		}

		// determine the (working + non-working) day difference between the two dates
		$days = abs($e->dateDiff($s));

		// if it is an intraday difference one is finished very easily
		if ($days == 0) {
			return min($dwh, abs($e->hour - $s->hour)) * $sgn;
		}

		// initialize the duration var
		$duration = 0;

		// process the first day
		// take into account the first day if it is a working day!
		$day_endpoint = (($sgn > 0) ? $cal_day_end : $cal_day_start);
		$duration += $s->isWorkingDay() ? min($dwh, abs($day_endpoint - $s->hour)) : 0;
		$s->addDays(1 * $sgn);

		// end of processing the first day

		// calc workingdays between start and end
		for ($i=1; $i < $days; $i++) {
			$duration += $s->isWorkingDay() ? $dwh : 0;
			$s->addDays(1 * $sgn);
		}

		// take into account the last day in span only if it is a working day!
		$day_endpoint = (($sgn > 0) ? $cal_day_start : $cal_day_end);
		$duration += $s->isWorkingDay() ? min($dwh, abs($e->hour - $day_endpoint)) : 0;

		return $duration * $sgn;
	}

	function workingDaysInSpan($e) {
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

		for ($i = 0 ; $i <= $days ; $i++) {
			if ($start->isWorkingDay()) {
				$wd++;
			}
			$start->addDays(1 * $sgn);
		}

		return $wd;
	}
}
