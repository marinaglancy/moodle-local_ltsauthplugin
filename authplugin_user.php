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
 * Provides the interface for overall managing of items
 *
 * @package    local_ltsauthplugin
 * @copyright  2018 Justin Hunt  {@link http://poodll.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use \local_ltsauthplugin\constants;
use \local_ltsauthplugin\subscription\usersubmanager;
use \local_ltsauthplugin\user\usersitemanager;
use \local_ltsauthplugin\user\usermanager;

admin_externalpage_setup('ltsauthplugin/authplugin_user');
$PAGE->set_title(get_string('authplugin_user', 'local_ltsauthplugin'));
$PAGE->set_heading(get_string('authplugin_user', 'local_ltsauthplugin'));

// Display a selectors so we can update contributor, site and sitecourseid.
$userselector = new \local_ltsauthplugin\userselector('selecteduser', array());
$selecteduser = $userselector->get_selected_user();

// Set up renderer and nav.
/** @var local_ltsauthplugin\output\renderer $renderer */
$renderer = $PAGE->get_renderer('local_ltsauthplugin');
echo $renderer->header(get_string('authplugin_user', 'local_ltsauthplugin'), 2);
echo $renderer->user_selection_form($userselector);

if ($selecteduser) {
    global $DB;
    /** @var \local_ltsauthplugin\persistent\user $ltsuser */
    $ltsuser = \local_ltsauthplugin\persistent\user::get_record(['userid' => $selecteduser->id]);
    if (!$ltsuser) {
        $ltsuser = new \local_ltsauthplugin\persistent\user();
        usermanager::save($ltsuser, (object)['userid' => $selecteduser->id]);
    }
    if ($ltsuser) {
        $authpluginuser = $ltsuser->to_record();

        $siteitems = usersitemanager::get_user_sites_for_display($authpluginuser->id);
        $subsitems = usersubmanager::get_user_subs_for_display($authpluginuser->id);

        echo $renderer->show_user_summary($selecteduser, $authpluginuser);
        echo $renderer->add_siteitem_link($selecteduser, $authpluginuser);
        echo $renderer->show_siteitems_list($siteitems);
        echo $renderer->add_subsitem_link($selecteduser, $authpluginuser);
        echo $renderer->show_subsitems_list($subsitems);
    }
} else {
    echo $renderer->heading(get_string('nouserselected', 'local_ltsauthplugin'), 3, 'main');
}

echo $renderer->footer();