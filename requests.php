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

admin_externalpage_setup('ltsauthplugin/authplugin_requests');
$PAGE->set_title(get_string('requests', 'local_ltsauthplugin'));
$PAGE->set_heading(get_string('requests', 'local_ltsauthplugin'));

// Set up renderer and nav.
/** @var local_ltsauthplugin\output\renderer $renderer */
$renderer = $PAGE->get_renderer('local_ltsauthplugin');
echo $renderer->header(get_string('requests', 'local_ltsauthplugin'), 2);

$table = new \local_ltsauthplugin\requests_table('requestslog');
$table->out(20, false);

echo $renderer->footer();