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
 * Action for adding/editing a subscription.
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

use \local_ltsauthplugin\subscription\submanager;

global $USER, $DB;

// First get the nfo passed in to set up the page.
$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', 'edit', PARAM_TEXT);

$url = new moodle_url('/local/ltsauthplugin/subscriptions/managesubs.php', array('id' => $id));
admin_externalpage_setup('ltsauthplugin/authplugin_subscription', '', null, $url);

$PAGE->set_title(get_string('addedititem', 'local_ltsauthplugin'));
$PAGE->set_heading(get_string('addedititem', 'local_ltsauthplugin'));

$item = new \local_ltsauthplugin\persistent\sub($id);

// We always head back to the authplugin items page.
$redirecturl = new moodle_url('/local/ltsauthplugin/authplugin_subscription.php', array());

// Handle delete actions.
if ($action == 'confirmdelete') {
    $renderer = $PAGE->get_renderer('local_ltsauthplugin');
    echo $renderer->header('subscriptions', null, get_string('confirmitemdeletetitle', 'local_ltsauthplugin'));
    echo $renderer->confirm(get_string("confirmitemdelete", "local_ltsauthplugin", $item->get('name')),
        new moodle_url('/local/ltsauthplugin/subscriptions/managesubs.php', array('action' => 'delete', 'id' => $id)),
        $redirecturl);
    echo $renderer->footer();
    exit;
} else if ($action == 'delete') {
    // Delete item now.
    require_sesskey();
    submanager::delete($item);
    redirect($redirecturl);
}

// Create the subscription form.
$mform = new \local_ltsauthplugin\form\sub(null, ['persistent' => $item]);

// If the cancel button was pressed, we are out of here.
if ($mform->is_cancelled()) {
    redirect($redirecturl);
} else if ($data = $mform->get_data()) {
    if (!isset($data->plugins)) {
        $data->plugins = [];
    }
    submanager::save($item, $data);
    redirect($redirecturl);
}

// If  we got here, there was no cancel, and no form data, so we are showing the form.
// If edit mode load up the item into a data object.
$renderer = $PAGE->get_renderer('local_ltsauthplugin');
$mode = 'usersites';
echo $renderer->header(get_string('edit', 'local_ltsauthplugin'));
$mform->display();
echo $renderer->footer();