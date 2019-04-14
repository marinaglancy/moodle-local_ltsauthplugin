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
 * Action for adding/editing a usersite.
 * replace i) local_ltsauthplugin eg MOD_CST, then ii) authplugin eg cst, then iii) usersite eg fbquestion, then iv) create a capability
 *
 * @package local_ltsauthplugin
 * @copyright  2014 Justin Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

require_once("../../../config.php");
require_once($CFG->libdir . '/adminlib.php');

use \local_ltsauthplugin\constants;
use \local_ltsauthplugin\user\usersitemanager;
use \local_ltsauthplugin\forms\usersiteform;

global $USER, $DB;

// first get the nfo passed in to set up the page
$userid = required_param('userid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);         // item id
$action = optional_param('action', 'edit', PARAM_TEXT);

$url = new moodle_url('/local/ltsauthplugin/usersite/manageusersites.php', ['userid' => $userid, 'id' => $id]);
admin_externalpage_setup('ltsauthplugin/authplugin_user', '', null, $url);

$PAGE->set_title(get_string('addedititem', 'local_ltsauthplugin'));
$PAGE->set_heading(get_string('addedititem', 'local_ltsauthplugin'));

// Are we in new or edit mode?
if ($id) {
    $item = $DB->get_record(constants::USERSITE_TABLE, array('id' => $id), '*');
    if (!$item) {
        print_error('could not find item of id:' . $id);
    }
    $edit = true;
} else {
    $edit = false;
}

// We always head back to the authplugin items page
$redirecturl = new moodle_url('/local/ltsauthplugin/authplugin_user.php', array('selecteduser' => $userid));

// Handle delete actions
if ($action == 'confirmdelete') {
    $renderer = $PAGE->get_renderer('local_ltsauthplugin');
    echo $renderer->header('usersites', null, get_string('confirmitemdeletetitle', 'authplugin'));
    echo $renderer->confirm(get_string("confirmitemdelete", "local_ltsauthplugin", $item->url1),
        new moodle_url('/local/ltsauthplugin/usersite/manageusersites.php', array('action' => 'delete', 'id' => $id, 'userid' => $userid)),
        $redirecturl);
    echo $renderer->footer();
    return;

} elseif ($action == 'delete') {
    // Delete item now.
    require_sesskey();
    $success = usersitemanager::delete_usersite($id);
    if (!$success) {
        print_error("Could not delete authplugin site!");
        redirect($redirecturl);
    }
    redirect($redirecturl);
}

// Create the usersite form
$mform = new usersiteform();

// If the cancel button was pressed, we are out of here
if ($mform->is_cancelled()) {
    redirect($redirecturl);
    exit;
}

// If we have data, then our job here is to save it and return to the main page
if ($data = $mform->get_data()) {
    require_sesskey();

    $theitem = new stdClass;
    $theitem->userid = $data->userid;
    $theitem->url1 = $data->url1;
    $theitem->wildcardok = 0;
    $theitem->expiredate = 0;
    $theitem->timemodified = time();

    // First insert a new item if we need to.
    // That will give us a itemid, we need that for saving files.
    if (!$edit) {
        $ret = usersitemanager::create_usersite($data->url1, $data->userid);
        if (!$ret) {
            print_error("Could not insert authplugin item!");
            redirect($redirecturl);
        }
    } else {
        $theitem->id = $id;
        $ret = usersitemanager::update_usersite($theitem->id, $data->url1, $data->userid);
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