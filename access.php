<?php
/* $Id$
 *
 * This page is used to manage user access rights.
 *
 * It has three different modes:
 * - list users to manage (no parameters)
 * - manage a single user's rights (just "user" parameter)
 *   this will include which functions the user can access and
 *   (if $allow_view_other is 'Y') which calendars thay can view/edit/approve
 * - update the database (form handler)
 *
 * Input Parameters:
 *  user - specifies which user to manage, a form will be presented
 *         that allows editing rights of this user
 *
 *  access_N - where N is 0 to ACCESS_NUMBER_FUNCTIONS as defined in
 *             includes/access.php. Each should be either 'Y' or 'N'.
 */
include_once 'includes/init.php';

$allow_view_other = getPref ( '_ALLOW_VIEW_OTHER' );

// Default value for Assistant Permissions if not set.
$asstApprove = $asstEdit = $asstView = 63;
$op = $otheruserList = array();
$otheruser_fullname = $saved = '';

$asstStr = translate ( 'ASSISTANT PERMISSIONS' );
$defStr = translate ( 'DEFAULT CONFIGURATION' );
$saveStr = translate ( 'Save' );

// print_r ( $_POST );
// Are we handling the access form? If so, do that, then redirect.
// Handle function access first.
if ( $WC->getPOST ( 'auser' ) != '' && $WC->getPOST ( 'submit' ) == $saveStr ) {
  $auser = $WC->getPOST ( 'auser' );
  $perm = '';
  for ( $i = 0; $i < ACCESS_NUMBER_FUNCTIONS; $i++ ) {
    $perm .= ( $WC->getPOST ( 'access_' . $i ) == 'Y' ? 'Y' : 'N' );
  }

  dbi_execute ( 'DELETE FROM webcal_access_function WHERE cal_login_id = ?',
    array ( $auser ) );

  if ( ! dbi_execute ( 'INSERT INTO webcal_access_function ( cal_login_id,
      cal_permissions ) VALUES ( ?, ? )', array ( $auser, $perm ) ) )
    die_miserable_death ( str_replace ( 'XXX', dbi_error(),
        translate ( 'Database error XXX.' ) ) );

  $saved = true;
}

// Are we handling the other user form? If so, do that, then redirect.
$getPostOtherUser = $WC->getPOST ( 'otheruser' );
$wcIsAdmin = $WC->isAdmin();

if ( $getPostOtherUser != '' && $WC->getPOST ( 'submit' ) == $saveStr ) {
  $pouser = $getPostOtherUser;
  $puser = $WC->getPOST ( 'guser' );

  if ( $allow_view_other ) {
    // Handle access to other users' calendars.
    // If user is not admin, reverse values so they are granting
    // access to their own calendar.
    if ( ! $wcIsAdmin )
      list ( $puser, $pouser ) = array ( $pouser, $puser );

    if ( empty ( $pouser ) )
      break;

    $approve_total = $edit_total = $view_total = 0;
    for ( $i = 1; $i < 257; ) {
      $approve_total += $WC->getPOST ( 'a_' . $i );
      $edit_total += $WC->getPOST ( 'e_' . $i );
      $view_total += $WC->getPOST ( 'v_' . $i );
      $i += $i;
    }

    $assistant = $WC->getPOST ( 'assistant' );
    $email = $WC->getPOST ( 'email' );
    $invite = $WC->getPOST ( 'invite' );
    $time = $WC->getPOST ( 'time' );

    $saved = set_user_UAC ( $puser, $pouser, $view_total, $edit_total,
      $approve_total, $invite, $email, $time, $assistant );
  }
}
$guser = $WC->getPOST ( 'guser' );

if ( $guser == UAC_DEFAULT )
  $otheruser = UAC_DEFAULT;
elseif ( $guser == UAC_ASSISTANT )
  $otheruser = UAC_ASSISTANT;
else
  $otheruser = $getPostOtherUser;

if ( $otheruser == UAC_DEFAULT ) {
  $otheruser_fullname = $defStr;
  $otheruser_login = UAC_DEFAULT;
} elseif ( $otheruser == UAC_ASSISTANT ) {
  $otheruser_fullname = $asstStr;
  $otheruser_login = UAC_ASSISTANT;
}
if ( ! empty ( $otheruser ) ) {
  if ( $allow_view_other ) {
    $other_user = $WC->User->loadVariables ( $otheruser );
    // Turn off admin override so we see the users own settings.
    // Now load all the data from webcal_access_user.
    $allPermissions = access_load_user_permissions ( false, false );

    // Load default-default values if exist.
    if ( ! empty ( $allPermissions[UAC_DEFAULT . '.' . UAC_DEFAULT] ) )
      $op = $allPermissions[UAC_DEFAULT . '.' . UAC_DEFAULT];

    // Load assistant-default values if exist.
    if ( ! empty ( $allPermissions[UAC_ASSISTANT . '.' . UAC_DEFAULT] ) ) {
      $asstApprove = $allPermissions[UAC_ASSISTANT . '.'
       . UAC_DEFAULT]['approve'];
      $asstEdit = $allPermissions[UAC_ASSISTANT . '.' . UAC_DEFAULT]['edit'];
      $asstView = $allPermissions[UAC_ASSISTANT . '.' . UAC_DEFAULT]['view'];
    }
    if ( $wcIsAdmin ) {
      // Load user-default values if exist.
      if ( ! empty ( $allPermissions[ $guser . '.' . UAC_DEFAULT] ) )
        $op = $allPermissions[ $guser . '.' . UAC_DEFAULT];

      // Load user-otheruser values if exist.
      if ( ! empty ( $allPermissions[ $guser . '.' . $otheruser ] ) )
        $op = $allPermissions[ $guser . '.' . $otheruser ];
    } else {
      // Load default-user values if exist.
      if ( ! empty ( $allPermissions[UAC_DEFAULT . '.' . $guser] ) )
        $op = $allPermissions[UAC_DEFAULT . '.' . $guser ];

      // Load assistant-user values if exist.
      if ( ! empty ( $allPermissions[UAC_ASSISTANT . '.' . $guser] ) ) {
        $asstApprove = $allPermissions[UAC_ASSISTANT . '.'
         . $guser ]['approve'];
        $asstEdit = $allPermissions[UAC_ASSISTANT . '.' . $guser ]['edit'];
        $asstView = $allPermissions[UAC_ASSISTANT . '.' . $guser ]['view'];
      }
      // Load otheruser-user values if exist.
      if ( ! empty ( $allPermissions[$otheruser . '.' . $guser] ) )
        $op = $allPermissions[$otheruser . '.' . $guser];
    }
  }
}
// Set up variable to pass if Assistant button is selected.
$smarty->assign ( 'asstWeight', $asstView . ',' . $asstEdit . ','
   . $asstApprove . ',1' );

build_header ( '', '',
  ( ! empty ( $op['time'] ) && $op['time'] == 'Y'
    ? 'onload="enableAll ( true );"' : '' ) );
echo print_success ( $saved );

if ( ! empty ( $guser ) && $wcIsAdmin )
  $smarty->assign ( 'userData', $WC->User->loadVariables ( $guser ) );

if ( $wcIsAdmin ) {
  $userlist = array_merge ( get_my_users(), get_nonuser_cals() );
  // If we are here... we must need to print out a list of users.
  // Add  these options as DEFAULTS.
  $users[0]['display'] = $defStr;
  $users[0]['value'] = UAC_DEFAULT;
  $users[1]['display'] = $asstStr;
  $users[1]['value'] = UAC_ASSISTANT;

  if ( $guser == UAC_DEFAULT )
    $users[0]['selected'] = SELECTED;

  if ( $guser == UAC_ASSISTANT )
    $users[1]['selected'] = SELECTED;

  for ( $i = count ( $userlist ) - 1; $i >= 0; $i-- ) {
    $users[$i + 2]['value'] = $userlist[$i]['cal_login_id'];
    if ( $guser == $userlist[$i]['cal_login_id'] )
      $users[$i + 2]['selected'] = SELECTED;

    $users[$i + 2]['display'] = $userlist[$i]['cal_fullname'];
  }
  $smarty->assign ( 'userlist', $users );
} //end admin $guser !- default test

if ( ! empty ( $guser ) || ! $wcIsAdmin ) {
  if ( $wcIsAdmin && $guser != UAC_ASSISTANT ) {
    // Present a page to allow editing a user's rights.
    $access = access_load_user_functions ( $guser );
    $div = ceil ( ACCESS_NUMBER_FUNCTIONS / 5 );
    $order = $GLOBALS['ACCESS_ORDER'];
    $access_functions = array();
    for ( $i = 0; $i < ACCESS_NUMBER_FUNCTIONS; $i++ ) {
      // Public access and NUCs can never use some of these functions.
      $show = true;
      if ( $show )
        $checked = '';

      $access_functions[$order[$i]]['desc'] =
      access_get_function_description ( $order[$i] );
      $access_functions[$order[$i]]['checked'] =
      ( substr ( $access, $order[$i], 1 ) == 'Y' ? CHECKED : '' );
      if ( ( $i + 1 ) % $div === 0 )
        $access_functions[$order[$i]]['closeTD'] = true;
    }
    $smarty->assign ( 'access_functions', $access_functions );
    $pagetitle = translate ( 'Allow Access to Other Users Calendar' );
  } else {
    $pagetitle = ( $guser == UAC_ASSISTANT
      ? translate ( 'Configure Default Assistant Access to My Calendar' )
      : translate ( 'Grant This User Access to My Calendar' ) );

    $guser = $WC->loginId();
  }

  if ( $guser == UAC_DEFAULT ) {
    $otheruser = $otheruser_login = UAC_DEFAULT;
    $otheruser_fullname = $defStr;
    $userlist = array ( UAC_DEFAULT );
  } elseif ( $guser == UAC_ASSISTANT ) {
    $otheruser = $otheruser_login = UAC_ASSISTANT;
    $otheruser_fullname = $asstStr;
    $userlist = array ( UAC_ASSISTANT );
  } else
  if ( $allow_view_other )
    $userlist = get_list_of_users ( $guser );

  if ( $otheruser != UAC_ASSISTANT ) {
    // Add  these options as DEFAULTS.
    $otheruserList[0]['value'] = UAC_DEFAULT;
    if ( $guser == UAC_DEFAULT )
      $otheruserList[0]['selected'] = SELECTED;

    $otheruserList[0]['display'] = $defStr;

    for ( $i = count ( $userlist ) - 1; $i >= 0; $i-- ) {
      if ( $userlist[$i]['cal_login_id'] != $guser )
        $otheruserList[$i + 1]['value'] = $userlist[$i]['cal_login_id'];

      if ( ! empty ( $otheruser ) && $otheruser == $userlist[$i]['cal_login_id'] )
        $otheruserList[$i + 1]['selected'] = SELECTED;

      $otheruserList[$i + 1]['display'] = $userlist[$i]['cal_fullname'];
    }
  }
  $smarty->assign ( 'otheruserList', $otheruserList );
}

$smarty->assign ( 'access_type', array ( '',
    translate ( 'Events' ),
    translate ( 'Tasks' ), '',
    translate ( 'Journals' ) ) );

$smarty->assign ( 'guser', $guser );
$smarty->assign ( 'otheruser', $otheruser );
$smarty->assign ( 'otheruser_fullname', $otheruser_fullname );
$smarty->assign ( 'op', $op );

$smarty->display ( 'access.tpl' );

/* Get the list of users that the specified user can see.
 */
function get_list_of_users ( $guser ) {
  global $WC;

  $u = get_my_users ( $guser, 'view' );
  if ( $WC->isAdmin() || $WC->isNonuserAdmin() )
    // Get public NUCs also.
    $u = array_merge ( get_my_nonusers ( $guser, true ), $u );

  return $u;
}

?>
