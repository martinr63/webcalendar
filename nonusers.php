<?php
include_once 'includes/init.php';
print_header();

if ( ! $is_admin ) {
  echo "<H2><FONT COLOR=\"$H2COLOR\">" . translate("Error") .
    "</FONT></H2>" . translate("You are not authorized") . ".\n";
  include_once "includes/trailer.php";
  echo "</BODY></HTML>\n";
  exit;
}
if ( ! $NONUSER_PREFIX ) {
  echo "<H2><FONT COLOR=\"$H2COLOR\">" . translate("Error") .
    "</FONT></H2>" . translate("NONUSER_PREFIX not set") . ".\n";
  include_once "includes/trailer.php";
  echo "</BODY></HTML>\n";
  exit;
}
?>

<H2><FONT COLOR="<?php echo $H2COLOR;?>"><?php etranslate("NonUser")?></FONT></H2>

<?php
// Adding/Editing category
if ( ( ( $add == '1' ) || ( ! empty ( $id ) ) ) && empty ( $error ) ) {
  $userlist = get_my_users ();
  $button = translate("Add");
  ?>
  <FORM ACTION="nonusers_handler.php" METHOD="POST">
  <?php
  if ( ! empty ( $id ) ) {
    nonuser_load_variables ( $id, 'nonusertemp_' );
    $id_display = "$id <INPUT NAME=\"id\" TYPE=\"hidden\" VALUE=\"$id\">";
    $button = translate("Save");
    $nonusertemp_login = substr($nonusertemp_login, strlen($NONUSER_PREFIX));
  } else {
    $id_display = "<INPUT NAME=\"id\" SIZE=\"20\" MAX=\"20\">";
  }
  ?>
  <table>
  <tr><td><?php etranslate("Calendar ID")?>:</td><td> <?php echo $id_display ?></td></tr>
  <tr><td><?php etranslate("First Name")?>:</td><td> <INPUT NAME="nfirstname" SIZE="20" MAX="25" VALUE="<?php echo htmlspecialchars ( $nonusertemp_firstname ); ?>"></td></tr>
  <tr><td><?php etranslate("Last Name")?>:</td><td> <INPUT NAME="nlastname" SIZE="20" MAX="25" VALUE="<?php echo htmlspecialchars ( $nonusertemp_lastname ); ?>"></td></tr>
  <tr><td><?php etranslate("Admin")?>:</td><td><SELECT NAME="nadmin">
  <?php
  for ( $i = 0; $i < count ( $userlist ); $i++ ) {
    echo "<OPTION VALUE=\"".$userlist[$i]['cal_login']."\"";
    if ($nonusertemp_admin == $userlist[$i]['cal_login'] ) echo " SELECTED";
    echo ">".$userlist[$i]['cal_fullname']."\n";
  }
  ?>
  </SELECT></td></tr>
  </table>

  <BR><BR>
  <INPUT TYPE="submit" NAME="action" VALUE="<?php echo $button;?>">
  <?php if ( ! empty ( $id ) ) {  ?>
    <INPUT TYPE="submit" NAME="action" VALUE="<?php etranslate("Delete");?>" ONCLICK="return confirm('<?php etranslate("Are you sure you want to delete this entry?"); ?>')">
  <?php }  ?>
  </FORM>
  <?php
} else if ( empty ( $error ) ) {
  // Displaying NonUser Calendars
  $userlist = get_nonuser_cals ();
  if ( ! empty ( $userlist ) ) {
    echo "<UL>";
    for ( $i = 0; $i < count ( $userlist ); $i++ ) {
      echo "<LI><A HREF=\"nonusers.php?id=" . $userlist[$i]["cal_login"] . "\">"
          . $userlist[$i]['cal_fullname'] . "</A></LI>\n";
    }
    echo "</UL>";
  }
  echo "<P><A HREF=\"nonusers.php?add=1\">" . translate("Add New NonUser Calendar") . "</A></P><BR>\n";
}
?>

<?php include_once "includes/trailer.php"; ?>
</BODY>
</HTML>