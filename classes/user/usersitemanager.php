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
 * class usersitemanager
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\user;

defined('MOODLE_INTERNAL') || die();

use \local_ltsauthplugin\constants;

/**
 *
 * This is a class containing functions for sending authplugins
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class usersitemanager {

    /**
     * Delete a usersite
     *
     * @param int $siteid
     * @return bool
     */
    public static function delete_usersite($siteid) {
        global $DB;
        $ret = $DB->delete_records(constants::USERSITE_TABLE, array('id' => $siteid));
        return $ret;
    }

    /**
     * Create a  particular user sites
     *
     * @param int $id
     * @return mixed
     */
    public static function get_usersite($id) {
        global $DB;
        $site = $DB->get_record(constants::USERSITE_TABLE, array('id' => $id));
        return $site;
    }

    /**
     * Get a list of user sites ready for display
     *
     * @param int $userid
     * @return array|bool
     */
    public static function get_usersites_fordisplay($userid) {
        global $DB;
        $ret = false;

        $sites = $DB->get_records_sql('SELECT ust.*, u.username FROM {' . constants::USERSITE_TABLE .
            '} ust INNER JOIN {user} u ON u.id = ust.userid WHERE userid = ?'
            , array($userid));

        if ($sites) {
            return $sites;
        }

        return $ret;
    }

    /**
     * get user by username
     *
     * @param string $username
     * @return mixed
     */
    public static function get_authpluginuser_by_username($username) {
        global $DB;

        $authpluginuser = $DB->get_record_sql("SELECT authplugin.* FROM {" . constants::USER_TABLE .
            "} authplugin INNER JOIN {user} u ON u.id = authplugin.userid WHERE u.username = ?;", array($username));
        return $authpluginuser;
    }

    /**
     * Create a new usersite
     *
     * @param string $url
     * @param bool $userid
     * @param string $note
     * @return bool|int
     */
    public static function create_usersite($url, $userid = false, $note = '') {
        global $DB, $USER;
        $ret = false;

        // If the userid was not passed in, then we use the current user.
        // This will be when added from webservice.
        if (!$userid) {
            $userid = $USER->id;
        }

        $thesite = new \stdClass;
        $thesite->userid = $userid;
        $thesite->url = $url;
        $thesite->note = $note;
        $thesite->timemodified = time();

        $thesite->id = $DB->insert_record(constants::USERSITE_TABLE, $thesite);
        $ret = $thesite->id;

        return $ret;
    }

    /**
     * update usersite
     *
     * @param int $id
     * @param string $url
     * @param int $userid
     * @param string $note
     * @return bool
     */
    public static function update_usersite($id, $url, $userid, $note) {
        global $DB;

        // It should not be possible to not pass in a userid here.
        if (!$userid) {
            return false;
        }

        // Build siteurl object.
        $thesite = new \stdClass;
        $thesite->id = $id;
        $thesite->userid = $userid;
        $thesite->url = $url;
        $thesite->note = $note;
        $thesite->timemodified = time();

        // Execute updaet and return.
        $ret = $DB->update_record(constants::USERSITE_TABLE, $thesite);
        return $ret;
    }
}