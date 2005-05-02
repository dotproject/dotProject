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
		$this->setDate( $this->getTime() + 60 * 60 * 24 * $n, DATE_FORMAT_UNIXTIME);
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
}
?>
