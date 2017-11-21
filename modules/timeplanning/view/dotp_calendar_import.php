<!-- import the calendar script -->
<script type="text/javascript" src="<?php echo DP_BASE_URL; ?>/lib/calendar/calendar.js"></script>
<!-- import the language module -->
<script type="text/javascript" src="<?php echo DP_BASE_URL; ?>/lib/calendar/lang/calendar-<?php echo $AppUI->user_locale; ?>.js"></script>
<script type="text/javascript" language="javascript">
    var calendarField = null;
    var calendarHiddenField=null;
    var calWin = null;

    function popCalendar( field,fieldHidden ){
        calendarField = field;
        calendarHiddenField =fieldHidden;
        idate = fieldHidden.value;
        window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=280, height=250, scrollbars=no' );
    }

    /**
     *	@param string Input date in the format YYYYMMDD
     *	@param string Formatted date
     */
    function setCalendar( idate, fdate ) {
        calendarField.value =fdate ;
        calendarHiddenField.value = idate;
    }
</script>
