<?php
include 'config.inc.php';

$self = $_SERVER['PHP_SELF'];
$request = $_SERVER['REQUEST_METHOD'];

// set cookie if 'Remember Me?' checkbox is checked, or reset cookie if 'Reset Cookie?' is checked //

if ($request == 'POST'){
    @$remember_me = $_POST['remember_me'];
    @$reset_cookie = $_POST['reset_cookie'];
    @$fullname = stripslashes($_POST['left_fullname']);
    @$displayname = stripslashes($_POST['left_displayname']);
	if(strlen($_POST['left_projects']) > 0) $projects = explode(',', $_POST['left_projects']);
    if ((isset($remember_me)) && ($remember_me != '1')) {echo "Something is fishy here.\n"; exit;}
    if ((isset($reset_cookie)) && ($reset_cookie != '1')) {echo "Something is fishy here.\n"; exit;}

    // begin post validation //

    if ($show_display_name == "yes") {

        if (isset($displayname)) {
            $displayname = addslashes($displayname);
            $query = "select displayname from ".$db_prefix."jobs where displayname = '".$displayname."'";
            $emp_name_result = mysql_query($query);

            while ($row = mysql_fetch_array($emp_name_result)) {
                $tmp_displayname = "".$row['displayname']."";
            }
            if ((!isset($tmp_displayname)) && (!empty($displayname))) {echo "Jobname is not in the database.\n"; exit;}
            $displayname = stripslashes($displayname);
        }

    }

    elseif ($show_display_name == "no") {

        if (isset($fullname)) {
            $fullname = addslashes($fullname);
            $query = "select jobname from ".$db_prefix."jobs where jobname = '".$fullname."'";
            $emp_name_result = mysql_query($query);

            while ($row = mysql_fetch_array($emp_name_result)) {
                $tmp_jobname = "".$row['jobname']."";
            }
            if ((!isset($tmp_jobname)) && (!empty($fullname))) {echo "Jobname is not in the database.\n"; exit;}
            $fullname = stripslashes($fullname);
        }

    }

    // end post validation //

    if (isset($remember_me)) {

        if ($show_display_name == "yes") {
            setcookie("remember_me", stripslashes($displayname), time() + (60 * 60 * 24 * 365 * 2));
        }

        elseif ($show_display_name == "no") {
            setcookie("remember_me", stripslashes($fullname), time() + (60 * 60 * 24* 365 * 2));
        }

    }

    elseif (isset($reset_cookie)) {
        setcookie("remember_me", "", time() - 3600);
    }

    ob_end_flush();
}

echo "<div class='row'>\n";

// If there are errors with the form submission, let's show an alert above the form here
if ($request == 'POST') {

	$errors = 0;
    // signin/signout data passed over from timeclock.php //

    $inout = $_POST['left_inout'];
    $notes = preg_replace("/[^[:alnum:] \,\.\?-]/","",strtolower($_POST['left_notes']));

    // begin post validation //

    if ($use_passwd == "yes") {
        $employee_passwd = crypt($_POST['employee_passwd'], 'xy');
    }

    $query = "select punchitems from ".$db_prefix."punchlist";
    $punchlist_result = mysql_query($query);

    while ($row = mysql_fetch_array($punchlist_result)) {
        $tmp_inout = "".$row['punchitems']."";
    }

    if (!isset($tmp_inout)) {echo "<div class='alert alert-danger'>In/Out Status is not in the database.</div>\n"; exit;}

    // end post validation //

    if ($show_display_name == "yes") {

        if (!$displayname && !$inout) {
            echo "<div class='alert alert-warning'>You have not chosen a username or a status. Please try again.</div>\n";
            $errors++;
        }

        if (!$displayname) {
            echo "<div class='alert alert-warning'>You have not chosen a username. Please try again.</div>\n";
            $errors++;
        }

    }

    elseif ($show_display_name == "no") {

        if (!$fullname && !$inout) {
            echo "<div class='alert alert-warning'>You have not chosen a username or a status. Please try again.</div>\n";
            $errors++;
        }

        if (!$fullname) {
            echo "<div class='alert alert-warning'>You have not chosen a username. Please try again.</div>\n";
            $errors++;
        }

    }

    if (!$inout) {
        echo "<div class='alert alert-warning'>You have not chosen a status. Please try again.</div>\n";
        $errors++;
    }

    @$fullname = addslashes($fullname);
    @$displayname = addslashes($displayname);

	@$name_array = explode(' ', $displayname);
	@$firstname = $name_array[0];

    // configure timestamp to insert/update //

    $time = time();
    $hour = gmdate('H',$time);
    $min = gmdate('i',$time);
    $sec = gmdate('s',$time);
    $month = gmdate('m',$time);
    $day = gmdate('d',$time);
    $year = gmdate('Y',$time);
    $tz_stamp = mktime ($hour, $min, $sec, $month, $day, $year);

	if($errors === 0) {
		if ($use_passwd == "no") {

			if ($show_display_name == "yes") {

				$sel_query = "select jobname from ".$db_prefix."jobs where displayname = '".$displayname."'";
				$sel_result = mysql_query($sel_query);

				while ($row=mysql_fetch_array($sel_result)) {
					$fullname = stripslashes("".$row["jobname"]."");
					$fullname = addslashes($fullname);
				}
			}

			if (strtolower($ip_logging) == "yes") {
				$query = "insert into ".$db_prefix."info (fullname, `inout`, timestamp, notes, ipaddress) values ('".$fullname."', '".$inout."',
						  '".$tz_stamp."', '".$notes."', '".$connecting_ip."')";
			} else {
				$query = "insert into ".$db_prefix."info (fullname, `inout`, timestamp, notes) values ('".$fullname."', '".$inout."', '".$tz_stamp."',
						  '".$notes."')";
			}

			$result = mysql_query($query);

			$update_query = "update ".$db_prefix."jobs set tstamp = '".$tz_stamp."' where jobname = '".$fullname."'";
			$other_result = mysql_query($update_query);

	//        echo "<head>\n";
	//        echo "<meta http-equiv='refresh' content=0;url=index.php>\n";
	//        echo "</head>\n";

		} else {

		  if ($show_display_name == "yes") {
			  $sel_query = "select jobname, employee_passwd from ".$db_prefix."jobs where displayname = '".$displayname."'";
			  $sel_result = mysql_query($sel_query);

			  while ($row=mysql_fetch_array($sel_result)) {
				  $tmp_password = "".$row["employee_passwd"]."";
				  $fullname = "".$row["jobname"]."";
			  }

			  $fullname = stripslashes($fullname);
			  $fullname = addslashes($fullname);

		  } else {

			  $sel_query = "select jobname, employee_passwd from ".$db_prefix."jobs where jobname = '".$fullname."'";
			  $sel_result = mysql_query($sel_query);

			  while ($row=mysql_fetch_array($sel_result)) {
				  $tmp_password = "".$row["employee_passwd"]."";
			  }

		  }

		  if ($employee_passwd == $tmp_password && $errors === 0) {

			  $last_time_query = "select e.tstamp, i.inout from ".$db_prefix."jobs e left join ".$db_prefix."info i on i.timestamp = e.tstamp AND i.fullname = e.jobname where jobname='" . $fullname . "' limit 1";
			  $last_time_array = mysql_fetch_array(mysql_query($last_time_query));
			  $last_time = $last_time_array['tstamp'];

			  if($inout != $last_time_array['inout']) {	  // prevent duplicate ins and outs

				if (strtolower($ip_logging) == "yes") {
					$query = "insert into ".$db_prefix."info (fullname, `inout`, timestamp, notes, ipaddress) values ('".$fullname."', '".$inout."',
							  '".$tz_stamp."', '".$notes."', '".$connecting_ip."')";
				} else {
					$query = "insert into ".$db_prefix."info (fullname, `inout`, timestamp, notes) values ('".$fullname."', '".$inout."', '".$tz_stamp."',
							  '".$notes."')";
				}
				$result = mysql_query($query);

				$update_query = "update ".$db_prefix."jobs set tstamp = '".$tz_stamp."' where jobname = '".$fullname."'";
				$other_result = mysql_query($update_query);

//				$active_projects_query = "select * from ".$db_prefix."projects_hours ph left join ".$db_prefix."projects p on p.project = ph.project where employee='" . $fullname . "' and active=1";
//				$active_projects_result = mysql_query($active_projects_query);
//
//				if($active_projects_result && @mysql_num_rows($active_projects_result) > 0) {
//				  $active_projects = array();
//				  $active_projects_codes = array();
//				  $total_active_units = 0;
//				  while($row = mysql_fetch_array($active_projects_result)) {
//					$active_projects[] = $row;
//					$active_projects_codes[] = $row['project'];
//					$total_active_units += $row['units'];
//				  }
  //				echo '<pre>' . print_r($active_projects) . '</pre>';
//				}
//				if(isset($projects) && count($projects) > 0) {  // clocking in with projects, or switching projects
//				  $WORKetc = new WORKetc();
//				  foreach($projects as $project) {
//					$check_project_result = mysql_query("select project from projects where project = '" . $project . "'");
//					if(@mysql_num_rows($check_project_result) === 0) {
//					  $units = round((float)$WORKetc->GetInvoiceSearchResults(array('StartIndex' => 0,
//																					'FetchSize' => 1,
//																					'keywords' => $project,
//																					'Relation' => 0,
//																					'filter' => 'Any',
//																					'sort' => 'InvoiceID',
//																					'asc' => true))->Results->Invoice->Total/1000, 1);
//					  $project_main_query = "insert into ".$db_prefix."projects (project, units, date_started) values('" . $project . "', '" . $units . "', NOW())";
//					  $project_main_result = mysql_query($project_main_query);
//					}
//					$project_main_update_query = "update ".$db_prefix."projects set date_last_modified=NOW() where project='" . $project . "'";
//					$project_update_query = "insert into ".$db_prefix."projects_hours (employee, project, active) values('" . $fullname . "', '" . $project . "', 1) on duplicate key update active=1";
//					$project_main_update_result = mysql_query($project_main_update_query);
//					$project_update_result = mysql_query($project_update_query);
//				  }
//				  $working_on .= "Working on :<ul><li>" . implode('</li></li>', $projects) . "</li></ul>";
//				}
//				if($inout !== 'IN') { // clocking out, calculate time difference from last timestamp (IN)
//				  if(isset($active_projects)) {
//					foreach($active_projects as $active_project) {
//					  $hours = ($tz_stamp - $last_time)/3600;
//					  $weighted_hours = $active_project['units']/$total_active_units * $hours;
//					  $active_project_update_query = "update ".$db_prefix."projects_hours set hours = hours + $weighted_hours, active = 0 where employee='" . $fullname . "' and project='" . $active_project['project'] . "'";
//  //					echo $active_project_update_query;
//					  $active_project_update_result = mysql_query($active_project_update_query);
//					}
//				  }
                                if($inout === 'lunch') echo "<script>$(function() { $('#lunchModal').modal(); setTimeout('clearModal(\"#lunchModal\")', 5000); });</script>";
                                elseif($inout === 'out') echo "<script>$(function() { $('#clockoutModal').modal(); setTimeout('clearModal(\"#clockoutModal\")', 5000); });</script>";
                                else echo "<script>$(function() { $('#clockinModal').modal(); setTimeout('clearModal(\"#clockinModal\")', 10000); });</script>";
			  } else { // duplicate entry of IN, OUT, Lunch
				echo "<script>$(function() { $('#dupeModal').modal(); setTimeout('clearModal(\"#dupeModal\")', 3000); });</script>";
			  }
			} else {

			  if ($show_display_name == "yes") {
				  $strip_fullname = stripslashes($displayname);
			  } else {
				  $strip_fullname = stripslashes($fullname);
			  }

			  echo "<script>$(function() { $('#wrongpassModal').modal(); setTimeout('refresh()', 1000); });</script>";
			  $retry = true;
			}
		}
	}
}
// display form to submit signin/signout information //

echo "      <form class='form' name='timeclock' action='$self' method='post' role='form'>\n";
echo "		  <div class='form-group'>";
//echo "			<label class='control-label' for='left_displayname'>Name:</label>\n";

// query to populate dropdown with employee names //

if ($show_display_name == "yes") {

    $query = "select displayname,groups,office from ".$db_prefix."jobs where disabled <> '1'  and jobname <> 'admin' order by displayname";
    $emp_name_result = mysql_query($query);
    echo "              <select id='name_entry' class='form-control input-lg ' style='background: #39b3d7; color: #FFF;' name='left_displayname' tabindex=1>\n";
    echo "              <option id='name_select' disabled selected value =''>&nbsp;Select job...</option>\n";
    while ($row = mysql_fetch_array($emp_name_result)) {
        $abc = stripslashes("".$row['displayname']."");

        if ((isset($_COOKIE['remember_me']) && (stripslashes($_COOKIE['remember_me']) == $abc))
				|| (isset($displayname) && $row['displayname'] == $displayname && $retry)) {
            echo "              <option selected>$abc</option>\n";
        } else {
            echo "              <option>$abc</option>\n";
        }

    }

    echo "              </select>
					  </div>\n";
    mysql_free_result($emp_name_result);

} else {

    $query = "select jobname from ".$db_prefix."jobs where disabled <> '1'  and jobname <> 'admin' order by jobname";
    $emp_name_result = mysql_query($query);
    echo "              <select id='name_entry' class='form-control input-lg' style='background: #39b3d7; color: #FFF;' name='left_fullname' tabindex=1>\n";
    echo "              <option id='name_select' disabled selected value =''>&nbsp;Name...</option>\n";

    while ($row = mysql_fetch_array($emp_name_result)) {

        $def = stripslashes("".$row['jobname']."");
        if (((isset($_COOKIE['remember_me'])) && (stripslashes($_COOKIE['remember_me']) == $def))) {
            echo "              <option selected>$def</option>\n";
        } else {
            echo "              <option>$def</option>\n";
        }

    }

    echo "              </select>
					  </div>\n";
    mysql_free_result($emp_name_result);
}

// determine whether to use encrypted passwords or not //

if ($use_passwd == "yes") {
	echo "		  <div class='form-group'>";
//    echo "			<label class='control-label' for='employee_passwd'>Password:</label>\n";
    echo "			  <input class='form-control text-center' disabled type='text' id='employee_pswd' name='employee_passwd' maxlength='25' placeholder='Key in password...' tabindex=2 />\n";
	include 'keypad.php';
	echo "		  </div>";
}
echo "		<div class='row'>&nbsp;</div>";
echo "		<div class='row'>";
//echo "        <label class='control-label' for='left_inout'>In/Out:</label><div class='clearfix'></div>\n";
// query to populate dropdown with punchlist items //

$query = "select punchitems from ".$db_prefix."punchlist";
$punchlist_result = mysql_query($query);

$index = 3;
while ($row = mysql_fetch_array($punchlist_result)) {
	$click = "";
    switch ($row['punchitems']) {
	  case 'in' :
		$btn_class = 'btn-success projects_box';
		$click = "onclick='submitTime(\"in\");'";
		break;
	  case 'out' :
		$btn_class = 'btn-danger';
		$click = "onclick='submitTime(\"out\");'";
		break;
	  case 'lunch' :
		$btn_class = 'btn-primary';
		$click = "onclick='submitTime(\"lunch\");'";
		break;
	}
	if($index < 6) {
	  echo "              <div class='col-xs-4'><input type='button' id='".$row['punchitems']."_btn' class='btn btn-block $btn_class' name='left_inout' value='".$row['punchitems']."' tabindex=".$index." $click /></div>\n";
	}
	$index++;
}
echo "				  <div class='clearfix'> </div>";
echo "				  </div>\n";
mysql_free_result( $punchlist_result );
echo "		  <div class='form-group'>";
//echo "			<label class='control-label' for='left_notes'>Notes:</label>\n";
echo "			  <textarea class='form-control' name='left_notes' maxlength='250' rows='1' placeholder='Add a note...' tabindex=".$index."></textarea>\n";
echo "			  </div>";

//if (!isset($_COOKIE['remember_me'])) {
//    echo "        <tr><td width=100%><table width=100% border=0 cellpadding=0 cellspacing=0>
//                  <tr><td nowrap height=4 align=left valign=middle class=misc_items width=10%>Remember&nbsp;Me?</td><td width=90% align=left
//                    class=misc_items style='padding-left:0px;padding-right:0px;' tabindex=5><input type='checkbox' name='remember_me' value='1'></td></tr>
//                    </table></td><tr>\n";
//}
//
//elseif (isset($_COOKIE['remember_me'])) {
//    echo "        <tr><td width=100%><table width=100% border=0 cellpadding=0 cellspacing=0>
//                  <tr><td nowrap height=4 align=left valign=middle class=misc_items width=10%>Reset&nbsp;Cookie?</td><td width=90% align=left
//                    class=misc_items style='padding-left:0px;padding-right:0px;' tabindex=5><input type='checkbox' name='reset_cookie' value='1'></td></tr>
//                    </table></td><tr>\n";
//}
echo "			<input type='hidden' name='left_projects' id='projects_input' value='' />\n";
echo "        </form>\n";  //<tr><td height=4 align=left valign=middle class=misc_items><input type='submit' name='submit_button' value='Submit' align='center' tabindex=6></td></tr>
echo "      </div>\n";

?>
