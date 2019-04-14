<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Action for adding/editing a user subscription
 * replace i) local_ltsauthplugin eg MOD_CST, then ii) authplugin eg cst,
 * then iii) usersite eg fbquestion, then iv) create a capability
 *
 * @package local_ltsauthplugin
 * @copyright  2014 Justin Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

require_once("../../../config.php");
require_once($CFG->libdir . '/adminlib.php');

use \local_ltsauthplugin\constants;
use \local_ltsauthplugin\subscription\usersubmanager;
use \local_ltsauthplugin\forms\usersubform;

global $USER, $DB;

// First get the info passed in to set up the page.
$userid = required_param('userid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', 'edit', PARAM_TEXT);

$url = new moodle_url('/local/ltsauthplugin/subscriptions/managesubs.php', array('id' => $id));
admin_externalpage_setup('ltsauthplugin/authplugin_user', '', null, $url);

$PAGE->set_title(get_string('addedititem', 'local_ltsauthplugin'));
$PAGE->set_heading(get_string('addedititem', 'local_ltsauthplugin'));

// Are we in new or edit mode?
if ($id) {
    $item = $DB->get_record(constants::USERSUB_TABLE, array('id' => $id, 'userid' => $userid), '*');
    if (!$item) {
        print_error('could not find item of id:' . $id . ' userid: ' . $userid);
    }
    $edit = true;
} else {
    $edit = false;
}

// We always head back to the authplugin items page.
$redirecturl = new moodle_url('/local/ltsauthplugin/authplugin_user.php', array('selecteduser' => $userid));

// Handle delete actions.
if ($action == 'confirmdelete') {
    $renderer = $PAGE->get_renderer('local_ltsauthplugin');
    echo $renderer->header('usersubs', null, get_string('confirmitemdeletetitle', 'local_ltsauthplugin'));
    echo $renderer->confirm(get_string("confirmitemdelete", "local_ltsauthplugin", $item->subscriptionid),
        new moodle_url('/local/ltsauthplugin/subscriptions/manageusersubs.php',
            array('action' => 'delete', 'id' => $id, 'subscriptionid' => $item->subscriptionid, 'userid' => $userid)),
        $redirecturl);
    echo $renderer->footer();
    return;

} else if ($action == 'delete') {
    // Delete item now.
    require_sesskey();
    $success = usersubmanager::delete_usersub($item->userid, $item->subscriptionid);
    if (!$success) {
        print_error("Could not delete authplugin user sub!");
        redirect($redirecturl);
    }
    redirect($redirecturl);
}

// Create the usersite form.
$mform = new usersubform();

// If the cancel button was pressed, we are out of here.
if ($mform->is_cancelled()) {
    redirect($redirecturl);
    exit;
}

// If we have data, then our job here is to save it and return to the edit page.
if ($data = $mform->get_data()) {
    require_sesskey();

    $theitem = new stdClass;
    $theitem->userid = $data->userid;
    $theitem->subscriptionid = $data->subscriptionid;
    $theitem->transactionid = $data->transactionid;
    $theitem->expiredate = $data->expiredate;
    $theitem->timemodified = time();

    // First insert a new item if we need to.
    // That will give us a itemid, we need that for saving files.
    if (!$edit) {
        $ret = usersubmanager::create_usersub($theitem->subscriptionid, $theitem->transactionid,
            $theitem->expiredate, $theitem->userid);
        if (!$ret) {
            print_error("Could not insert authplugin item!");
            redirect($redirecturl);
        }
    } else {
        $theitem->id = $item->id;
        $ret = usersubmanager::update_usersub($theitem->subscriptionid, $theitem->transactionid,
            $theitem->expiredate, $theitem->userid);
        if (!$ret) {
            print_error("Could not update authplugin item!");
            redirect($redirecturl);
        }
    }

    // Go back to main page.
    redirect($redirecturl);
}

// If  we got here, there was no cancel, and no form data, so we are showing the form.
// If edit mode load up the item into a data object.
if ($edit) {
    $data = $item;
    $data->id = $item->id;
    $data->userid = $item->userid;
} else {
    $data = new stdClass;
    $data->id = null;
    $data->userid = $userid;
}

$mform->set_data($data);
$renderer = $PAGE->get_renderer('local_ltsauthplugin');
$mode = 'usersites';
echo $renderer->header(get_string('edit', 'local_ltsauthplugin'));
$mform->display();
echo $renderer->footer();