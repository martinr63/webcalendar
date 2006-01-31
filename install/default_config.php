<?php
$webcalConfig = array (
"ADD_LINK_IN_VIEWS"=>"N",
"ALLOW_ATTACH"=>"N",
"ALLOW_ATTACH_ANY"=>"N",
"ALLOW_ATTACH_PART"=>"N",
"ALLOW_COLOR_CUSTOMIZATION"=>"Y",
"ALLOW_COMMENTS"=>"N",
"ALLOW_COMMENTS_ANY"=>"N",
"ALLOW_COMMENTS_PART"=>"N",
"ALLOW_CONFLICT_OVERRIDE"=>"Y",
"ALLOW_CONFLICTS"=>"N",
"ALLOW_EXTERNAL_HEADER"=>"N",
"ALLOW_EXTERNAL_USERS"=>"N",
"ALLOW_HTML_DESCRIPTION"=>"Y",
"ALLOW_SELF_REGISTRATION"=>"N",
"ALLOW_USER_HEADER"=>"N",
"ALLOW_USER_THEMES"=>"Y",
"ALLOW_VIEW_OTHER"=>"Y",
"APPLICATION_NAME"=>"Title",
"APPROVE_ASSISTANT_EVENT"=>"Y",
"AUTO_REFRESH_TIME"=>"0",
"AUTO_REFRESH"=>"N",
"BGCOLOR"=>"#FFFFFF",
"BOLD_DAYS_IN_YEAR"=>"Y",
"CATEGORIES_ENABLED"=>"Y",
"CELLBG"=>"#C0C0C0",
"CONFLICT_REPEAT_MONTHS"=>"6",
"CUSTOM_HEADER"=>"N",
"CUSTOM_SCRIPT"=>"N",
"CUSTOM_TRAILER"=>"N",
"DATE_FORMAT_MD"=>"LANGUAGE_DEFINED",
"DATE_FORMAT_MY"=>"LANGUAGE_DEFINED",
"DATE_FORMAT"=>"LANGUAGE_DEFINED",
"DEMO_MODE"=>"N",
"DISABLE_ACCESS_FIELD"=>"N",
"DISABLE_LOCATION_FIELD"=>"N",
"DISABLE_PARTICIPANTS_FIELD"=>"N",
"DISABLE_POPUPS"=>"N",
"DISABLE_PRIORITY_FIELD"=>"N",
"DISABLE_REPEATING_FIELD"=>"N",
"DISPLAY_ALL_DAYS_IN_MONTH"=>"N",
"DISPLAY_CREATED_BYPROXY"=>"Y",
"DISPLAY_DESC_PRINT_DAY"=>"Y",
"DISPLAY_LOCATION"=>"N",
"DISPLAY_SM_MONTH"=>"Y",
"DISPLAY_TASKS"=>"N",
"DISPLAY_TASKS_IN_GRID"=>"N",
"DISPLAY_UNAPPROVED"=>"Y",
"DISPLAY_WEEKENDS"=>"Y",
"DISPLAY_WEEKNUMBER"=>"Y",
"EMAIL_ASSISTANT_EVENTS"=>"Y",
"EMAIL_EVENT_ADDED"=>"Y",
"EMAIL_EVENT_DELETED"=>"Y",
"EMAIL_EVENT_REJECTED"=>"Y",
"EMAIL_EVENT_UPDATED"=>"Y",
"EMAIL_FALLBACK_FROM"=>"youremailhere",
"EMAIL_HTML"=>"N",
"EMAIL_MAILER"=>"mail",
"EMAIL_REMINDER"=>"Y",
"ENABLE_GRADIENTS"=>"N",
"ENABLE_ICON_UPLOADS"=>'N',
"EXTERNAL_NOTIFICATIONS"=>"N",
"EXTERNAL_REMINDERS"=>"N",
"FREEBUSY_ENABLED"=>"N",
"FONTS"=>"Arial, Helvetica, sans-serif",
"GENERAL_USE_GMT"=>"Y",
"GROUPS_ENABLED"=>"N",
"HASEVENTSBG"=>"#FFFF33",
"H2COLOR"=>"#000000",
"ICS_TIMEZONES"=>"Y",
"IMPORT_CATEGORIES"=>"Y",
"LANGUAGE"=>"none",
"LIMIT_APPTS_NUMBER"=>"6",
"LIMIT_APPTS"=>"N",
"LIMIT_DESCRIPTION_SIZE"=>"N",
"MYEVENTS"=>"#006000",
"NONUSER_AT_TOP"=>"Y",
"NONUSER_ENABLED"=>"Y",
"OTHERMONTHBG"=>"#D0D0D0",
"OVERRIDE_PUBLIC_TEXT"=>"Not available",
"OVERRIDE_PUBLIC"=>"N",
"PARTICIPANTS_IN_POPUP"=>"N",
"PLUGINS_ENABLED"=>"N",
"POPUP_BG"=>"#FFFFFF",
"POPUP_FG"=>"#000000",
"PUBLIC_ACCESS_ADD_NEEDS_APPROVAL"=>"N",
"PUBLIC_ACCESS_CAN_ADD"=>"N",
"PUBLIC_ACCESS_DEFAULT_SELECTED"=>"N",
"PUBLIC_ACCESS_DEFAULT_VISIBLE"=>"N",
"PUBLIC_ACCESS_OTHERS"=>"Y",
"PUBLIC_ACCESS_VIEW_PART"=>"N",
"PUBLIC_ACCESS"=>"N",
"PUBLISH_ENABLED"=>"Y",
"PULLDOWN_WEEKNUMBER"=>"N",
"REMEMBER_LAST_LOGIN"=>"N",
"REPORTS_ENABLED"=>"N",
"REQUIRE_APPROVALS"=>"Y",
"RSS_ENABLED"=>"N",
"SELF_REGISTRATION_BLACKLIST"=>"N",
"SELF_REGISTRATION_FULL"=>"Y",
"SEND_EMAIL"=>"N",
"SERVER_TIMEZONE"=>"America/New_York",
"SITE_EXTRAS_IN_POPUP"=>"N",
"SMTP_AUTH"=>"N",
"SMTP_HOST"=>"localhost",
"SMTP_PASSWORD"=>"",
"SMTP_PORT"=>"25",
"SMTP_USERNAME"=>"",
"STARTVIEW"=>"month.php",
"SUMMARY_LENGTH"=>"80",
"TABLEBG"=>"#000000",
"TEXTCOLOR"=>"#000000",
"THBG"=>"#FFFFFF",
"THFG"=>"#000000",
"TIME_FORMAT"=>"12",
"TIME_SLOTS"=>"24",
"TIMED_EVT_LEN"=>"D",
"TIMEZONE"=>"",
"TODAYCELLBG"=>"#FFFF33",
"TZ_COMPLETE_LIST"=>"N",
"UAC_ENABLED"=>"N",
"USER_SEES_ONLY_HIS_GROUPS"=>"Y",
"WEBCAL_PROGRAM_VERSION"=>"v1.1.0c-CVS",
"WEEK_START"=>"0",
"WEEKENDBG"=>"#D0D0D0",
"WORK_DAY_END_HOUR"=>"17",
"WORK_DAY_START_HOUR"=>"8" 
 );

function make_uppercase () {
  //make sure all cal_settings are UPPERCASE
  if ( ! dbi_execute ( "UPDATE webcal_config SET cal_setting = UPPER(cal_setting)" ) )
    echo translate("Error updating webcal_config") . ": " . dbi_error ();       
  dbi_free_result ( $res );    
  if ( ! dbi_execute ( "UPDATE webcal_user_pref SET cal_setting = UPPER(cal_setting)" ) )
    echo translate("Error updating webcal_user_pref") . ": " . dbi_error ();       
  dbi_free_result ( $res );
}

function db_load_config () {
global $webcalConfig; 
   while ( list ( $key, $val ) = each ( $webcalConfig ) ) {
    $res = dbi_execute ( "SELECT cal_value FROM webcal_config " .
     "WHERE cal_setting  = ?", array( $key ) , false, false );
   $sql = "INSERT INTO webcal_config ( cal_setting, cal_value ) " .
       "VALUES (?,?)";
     if ( ! $res ) {
       dbi_execute  ( $sql , array ( $key , $val ) );
   } else { //Sqlite returns $res always
     $row = dbi_fetch_row ( $res );
     if ( ! isset ( $row[0] ) ){
         dbi_execute ( $sql , array ( $key , $val ) );  
     }
     dbi_free_result ( $res );
    }  
 }
}

function db_load_admin () {
 $res = dbi_execute ( "SELECT cal_login FROM webcal_user " .
 "WHERE cal_login  = 'admin'", array() , false, false );
 $sql = "INSERT INTO webcal_user ( cal_login, cal_passwd, cal_lastname, cal_firstname, cal_is_admin ) 
VALUES ( 'admin', '21232f297a57a5a743894a0e4a801fc3', 'ADMINISTRATOR', 'DEFAULT', 'Y' );";
 if ( ! $res ) {
  dbi_execute ( $sql );
 } else { //Sqlite returns $res always
  $row = dbi_fetch_row ( $res );
  if ( ! isset ( $row[0] ) ){
   dbi_execute ( $sql );  
  }
  dbi_free_result ( $res );
 }  
}

function do_v11b_updates () {
 $res = dbi_execute ( "SELECT webcal_entry_user.cal_id, cal_category, cat_owner " . 
   "FROM webcal_entry_user, webcal_categories " . 
   "WHERE webcal_entry_user.cal_category = webcal_categories.cat_id");
 if (  $res ) {
   while( $row = dbi_fetch_row ( $res ) ) {
     if (  ! empty ( $row[2] ) ) {
     dbi_execute ("INSERT INTO webcal_entry_categories ( cal_id, cat_id, cat_owner ) " .
       " VALUES (?,?,?)" , array ( $row[0], $row[1], $row[2] ) );  
      } else {
     dbi_execute ("INSERT INTO webcal_entry_categories ( cal_id, cat_id, cat_order ) " .
       " VALUES (?,?,?)" , array ( $row[0], $row[1], 99 ) );        
      }     
   }
   dbi_free_result ( $res );
 }

 //update LANGUAGE settings from Browser-Defined to none
 dbi_execute ("UPDATE webcal_config  SET cal_value = 'none'" .
    " WHERE cal_setting = 'LANGUAGE' AND cal_value = 'Browser-defined'");

 dbi_execute ("UPDATE webcal_user_pref  SET cal_value = 'none'" .
    " WHERE cal_setting = 'LANGUAGE' AND cal_value = 'Browser-defined'");
         
 //clear old category values
 dbi_execute ( "UPDATE webcal_entry_user SET cal_category = NULL");  
 //mark existing exclusions as new exclusion type
 dbi_execute ( "UPDATE webcal_entry_repeats_not  SET cal_exdate = 1");  
 //change cal_days format to cal_cal_byday format
 
 //deprecate monthlyByDayR to simply monthlyByDay
 dbi_execute ("UPDATE webcal_entry_repeats  SET cal_type = 'monthlyByDay'" .
    " WHERE cal_type = 'monthlybByDayR'");
 $res = dbi_execute ( "SELECT cal_id, cal_days FROM webcal_entry_repeats ");
 if (  $res ) {
   while( $row = dbi_fetch_row ( $res ) ) {
     if ( ! empty ( $row[1] ) && $row[1] != 'yyyyyyy' && $row[1] != 'nnnnnnn' ) {
       $byday = array();
       if ( substr( $row[1],0,1 ) == 'y') $byday[] = 'SU';
       if ( substr( $row[1],1,1 ) == 'y') $byday[] = 'MO';       
       if ( substr( $row[1],2,1 ) == 'y') $byday[] = 'TU';       
       if ( substr( $row[1],3,1 ) == 'y') $byday[] = 'WE';       
       if ( substr( $row[1],4,1 ) == 'y') $byday[] = 'TH';
       if ( substr( $row[1],5,1 ) == 'y') $byday[] = 'FR';
       if ( substr( $row[1],6,1 ) == 'y') $byday[] = 'SA';
       $bydays = implode (",", $byday );       
       dbi_execute ("UPDATE webcal_entry_repeats  SET cal_byday = ?" .
       " WHERE cal_id = ?" , array ( $bydays , $row[0] ) );
     }
   }
   dbi_free_result ( $res );
 }
}
?>
