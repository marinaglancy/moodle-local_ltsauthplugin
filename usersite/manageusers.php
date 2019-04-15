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

use \local_ltsauthplugin\user\usermanager;

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
    $item = \local_ltsauthplugin\persistent\user::get_record(['userid' => $userid]);
    if (!$item) {
        print_error('could not find authplugin_user entry of userid:' . $userid);
    }
} else {
    $item = new \local_ltsauthplugin\persistent\user();
}

// We always head back to the authplugin items page.
$redirecturl = new moodle_url('/local/ltsauthplugin/authplugin_user.php', array('selecteduser' => $userid));

// Get create our user form.
$mform = new \local_ltsauthplugin\form\user(null, ['persistent' => $item]);

// If the cancel button was pressed, we are out of here.
if ($mform->is_cancelled()) {
    redirect($redirecturl);
    exit;
}

// If we have data, then our job here is to save it and return to the main page.
if ($data = $mform->get_data()) {
    usermanager::save($item, $data);
    redirect($redirecturl);
}

// If  we got here, there was no cancel, and no form data, so we are showing the form.
// If edit mode load up the item into a data object.
if ($item->get('id')) {
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

