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
 * Action for adding/editing an app.
 * replace i) local_ltsauthplugin eg MOD_CST, then ii) authplugin eg cst, then iii) usersite eg fbquestion, then iv) create a capability
 *
 * @package local_ltsauthplugin
 * @copyright  2014 Justin Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

require_once("../../../config.php");
require_once($CFG->libdir . '/adminlib.php');

use \local_ltsauthplugin\constants;
use \local_ltsauthplugin\subscription\appmanager;
use \local_ltsauthplugin\forms\appform;

global $USER, $DB;

// first get the nfo passed in to set up the page
$id = optional_param('id', 0, PARAM_INT);         // item id
$action = optional_param('action', 'edit', PARAM_TEXT);

$url = new moodle_url('/local/ltsauthplugin/subscriptions/manageapps.php', array('id' => $id));
admin_externalpage_setup('ltsauthplugin/authplugin_subscription', '', null, $url);

$PAGE->set_title(get_string('addedititem', 'local_ltsauthplugin'));
$PAGE->set_heading(get_string('addedititem', 'local_ltsauthplugin'));

//are we in new or edit mode?
if ($id) {
    $item = $DB->get_record(constants::APP_TABLE, array('id' => $id), '*');
    if (!$item) {
        print_error('could not find item of app id:' . $id);
    }
    $edit = true;
} else {
    $edit = false;
}

//we always head back to the authplugin items page
$redirecturl = new moodle_url('/local/ltsauthplugin/authplugin_subscription.php', array());

//handle delete actions
if ($action == 'confirmdelete') {
    $renderer = $PAGE->get_renderer('local_ltsauthplugin');
    echo $renderer->header('apps', null, get_string('confirmitemdeletetitle', 'local_ltsauthplugin'));
    echo $renderer->confirm(get_string("confirmitemdelete", "local_ltsauthplugin", $item->appname),
        new moodle_url('/local/ltsauthplugin/subscriptions/manageapps.php', array('action' => 'delete', 'id' => $id)),
        $redirecturl);
    echo $renderer->footer();
    return;

    /////// Delete item NOW////////
} elseif ($action == 'delete') {
    require_sesskey();
    $success = appmanager::delete_app($item->appid);
    if (!$success) {
        print_error("Could not delete authplugin app!");
        redirect($redirecturl);
    }
    redirect($redirecturl);
}

//create the app form
$mform = new appform();

//if the cancel button was pressed, we are out of here
if ($mform->is_cancelled()) {
    redirect($redirecturl);
    exit;
}

//if we have data, then our job here is to save it and return to the quiz edit page
if ($data = $mform->get_data()) {
    require_sesskey();

    $theitem = new stdClass;
    $theitem->appid = $data->appid;
    $theitem->appname = $data->appname;
    $theitem->timemodified = time();

    //first insert a new item if we need to
    //that will give us a itemid, we need that for saving files
    if (!$edit) {
        $ret = appmanager::create_app($theitem->appid, $theitem->appname);
        if (!$ret) {
            print_error("Could not insert authplugin app!");
            redirect($redirecturl);
        }
    } else {
        $theitem->id = $id;
        if ($data->appid != $item->appid) {
            print_error("I am sorry but you can not edit the app id. Delete and remake it. Nothing will happen to the app ids in the subscription table");
            redirect($redirecturl);
        } else {
            $ret = appmanager::update_app($theitem->appid, $theitem->appname);
            if (!$ret) {
                print_error("Could not update authplugin app!");
                redirect($redirecturl);
            }
        }
    }

    //go back to edit quiz page
    redirect($redirecturl);
}


//if  we got here, there was no cancel, and no form data, so we are showing the form
//if edit mode load up the item into a data object
if ($edit) {
    $data = $item;
    $data->id = $item->id;
} else {
    $data = new stdClass;
    $data->id = null;
}

$mform->set_data($data);
$renderer = $PAGE->get_renderer('local_ltsauthplugin');
echo $renderer->header(get_string('edit', 'local_ltsauthplugin'));
$mform->display();
echo $renderer->footer();