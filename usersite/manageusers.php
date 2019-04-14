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
 *
 * replace i) local_ltsauthplugin eg MOD_CST, then ii) authplugin eg cst,
 * then iii) usersite eg fbquestion, then iv) create a capability
 *
 * @package local_ltsauthplugin
 * @copyright  2014 Justin Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../../config.php");
require_once($CFG->libdir . '/adminlib.php');

use \local_ltsauthplugin\constants;
use \local_ltsauthplugin\user\usermanager;
use \local_ltsauthplugin\forms\userform;

global $USER, $DB;

// First get the nfo passed in to set up the page.
$userid = required_param('userid', PARAM_INT); // Id in table {user}.
$action = optional_param('action', 'edit', PARAM_TEXT);

$url = new moodle_url('/local/ltsauthplugin/usersite/manageusers.php', ['userid' => $userid]);
admin_externalpage_setup('ltsauthplugin/authplugin_user', '', null, $url);

$PAGE->set_title(get_string('addedituser', 'local_ltsauthplugin'));
$PAGE->set_heading(get_string('addedituser', 'local_ltsauthplugin'));

// Are we in new or edit mode?
if ($userid) {
    $item = $DB->get_record(constants::USER_TABLE, array('userid' => $userid), '*');
    if (!$item) {
        print_error('could not find authplugin_user entry of userid:' . $userid);
    }
    $id = $item->id;
    $edit = true;
} else {
    $edit = false;
}

// We always head back to the authplugin items page.
$redirecturl = new moodle_url('/local/ltsauthplugin/authplugin_user.php', array('selecteduser' => $userid));

// Get create our user form.
$mform = new userform();

// If the cancel button was pressed, we are out of here.
if ($mform->is_cancelled()) {
    redirect($redirecturl);
    exit;
}

// If we have data, then our job here is to save it and return to the main page.
if ($data = $mform->get_data()) {
    require_sesskey();

    $theitem = new stdClass;
    $theitem->userid = $data->userid;
    $theitem->note = $data->note;
    $theitem->timemodified = time();

    // First insert a new item if we need to.
    // That will give us a itemid, we need that for saving files.
    if (!$edit) {

        $ret = usermanager::create_user(
            $data->note,
            $data->userid);

        if (!$ret) {
            print_error("Could not insert authplugin user!");
            redirect($redirecturl);
        }
    } else {

        $ret = usermanager::update_user($id,
            $data->note,
            $data->userid);

        if (!$ret) {
            print_error("Could not update authplugin user!");
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
    $data->userid = $userid;

    $mform->set_data($data);
    $renderer = $PAGE->get_renderer('local_ltsauthplugin');
    echo $renderer->header(get_string('edit', 'local_ltsauthplugin'));
    $mform->display();
    echo $renderer->footer();

} else {
    echo $renderer->header(get_string('edit', 'local_ltsauthplugin'));
    echo "you can not add new users here";
    echo $renderer->footer();
    return;
}

