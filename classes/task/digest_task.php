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
 * Class digest_task
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\task;

use local_ltsauthplugin\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Class digest_task
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class digest_task extends \core\task\scheduled_task {

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $PAGE;
        $renderer = $PAGE->get_renderer('local_ltsauthplugin');
        $stats = helper::display_tab_stats($renderer, false);
        if (!$stats) {
            return;
        }

        $users = get_users_by_capability(\context_system::instance(), 'local/ltsauthplugin:manage', 'u.*');
        foreach ($users as $user) {
            $eventdata = new \core\message\message();
            $eventdata->courseid         = SITEID;
            $eventdata->name             = 'statsdigest';
            $eventdata->component        = 'local_ltsauthplugin';
            $eventdata->userfrom         = \core_user::get_noreply_user();
            $eventdata->userto           = $user;
            $eventdata->subject          = 'LTS Auth Plugin statistics';
            $eventdata->fullmessage      = html_to_text($stats);
            $eventdata->fullmessageformat = FORMAT_HTML;
            $eventdata->fullmessagehtml  = $stats;
            $eventdata->smallmessage     = 'LTS Auth Plugin statistics';
            message_send($eventdata);
        }
    }

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('digesttask', 'local_ltsauthplugin');
    }
}
