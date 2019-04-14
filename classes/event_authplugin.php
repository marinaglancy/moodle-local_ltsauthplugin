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
 * class event_authplugin
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin;

defined('MOODLE_INTERNAL') || die();

use core\event\user_created;
use local_ltsauthplugin\user\usermanager;

/**
 * This is a class responding to Moodle create user events
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_authplugin {

    /**
     * Observer for user_created event
     *
     * Here we create a authplugin user table entry, when we get an event that a Moodle user has been created
     * The authplugin user table contains info about a users subscription
     *
     * @param user_created $event
     */
    public static function create_user_handler(user_created $event) {
        // Get the event data.
        $eventdata = $event->get_data();

        try {
            // User data.
            $userid = false;
            if (array_key_exists('relateduserid', $eventdata)) {
                $userid = $eventdata['relateduserid'];
                $eventdata['userid'] = $userid;
            } else if (array_key_exists('userid', $eventdata)) {
                $userid = $eventdata['userid'];
            }
            if ($userid) {
                $exists = usermanager::user_exists($userid);
                if (!$exists) {
                    $success = usermanager::create_user('', $userid);
                }
            }

        } catch (\Exception $error) {
            debugging("fetching user error for authplugin request  failed with error: " . $error->getMessage(), DEBUG_ALL);
        }
    }
}