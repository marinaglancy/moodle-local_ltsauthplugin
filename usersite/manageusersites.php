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
 * @package    local_ltsauthplugin
 * @copyright  2014 Justin Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../../config.php");
require_once($CFG->libdir . '/adminlib.php');

use \local_ltsauthplugin\constants;
use \local_ltsauthplugin\user\usersitemanager;
use \local_ltsauthplugin\forms\usersiteform;

global $USER, $DB;

// First get the nfo passed in to set up the page.
$ltsuserid = required_param('ltsuserid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', 'edit', PARAM_TEXT);

$url = new moodle_url('/local/ltsauthplugin/usersite/manageusersites.php', ['ltsuserid' => $ltsuserid, 'id' => $id]);
admin_externalpage_setup('ltsauthplugin/authplugin_user', '', null, $url);

$PAGE->set_title(get_string('addedititem', 'local_ltsauthplugin'));
$PAGE->set_heading(get_string('addedititem', 'local_ltsauthplugin'));

$item = new \local_ltsauthplugin\persistent\user_site($id);
if (!$id) {
    $item->set('ltsuserid', $ltsuserid);
}

// We always head back to the authplugin items page.
$ltsuser = new \local_ltsauthplugin\persistent\user($item->get('ltsuserid'));
$redirecturl = new moodle_url('/local/ltsauthplugin/authplugin_user.php', array('selecteduser' => $ltsuser->get('userid')));

// Handle delete actions.
if ($action == 'confirmdelete') {
    $renderer = $PAGE->get_renderer('local_ltsauthplugin');
    echo $renderer->header('usersites', null, get_string('confirmitemdeletetitle', 'local_ltsauthplugin'));
    echo $renderer->confirm(get_string("confirmitemdelete", "local_ltsauthplugin", $item->get('url')),
        new moodle_url('/local/ltsauthplugin/usersite/manageusersites.php',
            array('action' => 'delete', 'id' => $id, 'ltsuserid' => $ltsuserid)),
        $redirecturl);
    echo $renderer->footer();
    return;

} else if ($action == 'delete') {
    // Delete item now.
    require_sesskey();
    usersitemanager::delete($item);
    redirect($redirecturl);
}

// Create the usersite form.
$mform = new usersiteform();

// If the cancel button was pressed, we are out of here.
if ($mform->is_cancelled()) {
    redirect($redirecturl);
} else if ($data = $mform->get_data()) {
    require_sesskey();
    usersitemanager::save($item, $data);
    redirect($redirecturl);
}

// If  we got here, there was no cancel, and no form data, so we are showing the form.
// If edit mode load up the item into a data object.
$mform->set_data($item->to_record());
$renderer = $PAGE->get_renderer('local_ltsauthplugin');
$mode = 'usersites';
echo $renderer->header(get_string('edit', 'local_ltsauthplugin'));
$mform->display();
echo $renderer->footer();