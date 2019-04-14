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
 * @package local_ltsauthplugin
 * @copyright  2018 Justin Hunt  {@link http://poodll.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use \local_ltsauthplugin\subscription\submanager;
use \local_ltsauthplugin\subscription\appmanager;

admin_externalpage_setup('ltsauthplugin/authplugin_subscription');
$PAGE->set_title(get_string('authplugin_subscription', 'local_ltsauthplugin'));
$PAGE->set_heading(get_string('authplugin_subscription', 'local_ltsauthplugin'));

// Set up renderer and nav.
/** @var local_ltsauthplugin\output\renderer $renderer */
$renderer = $PAGE->get_renderer('local_ltsauthplugin');
echo $renderer->header(get_string('authplugin_subscription', 'local_ltsauthplugin'), 2);

$subs = submanager::get_subs();
$apps = appmanager::get_apps();
echo $renderer->add_sub_link();
echo $renderer->show_subs_list($subs);
echo $renderer->add_app_link();
echo $renderer->show_apps_list($apps);
echo $renderer->footer();
