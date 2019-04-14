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
 * class appmanager
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\subscription;

defined('MOODLE_INTERNAL') || die();

use \local_ltsauthplugin\constants;

/**
 * This is a class containing functions for storing info about apps
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class appmanager {
    /**
     * Delete an app
     * @param string $appname
     */
    public static function delete_app($appname) {
        global $DB;
        $ret = $DB->delete_records(constants::APP_TABLE, ['appname' => $appname]);
        return $ret;
    }

    /**
     * Get a  particular app
     *
     * @param string $appname
     */
    public static function get_app($appname) {
        global $DB;
        $app = $DB->get_record(constants::APP_TABLE, ['appname' => $appname]);
        return $app;
    }

    /**
     * Get a  particular app
     */
    public static function get_apps() {
        global $DB;
        $app = $DB->get_records(constants::APP_TABLE, array());
        return $app;
    }

    /**
     * Create a new app
     * @param string $appname
     * @param string $note
     */
    public static function create_app($appname, $note) {
        global $DB;

        // Make sure we do not already have this app. And if so, just update it.
        $theapp = $DB->get_record(constants::APP_TABLE, ['appname' => $appname]);
        if ($theapp) {
            throw new \moodle_exception('App with this name already exists');
        }

        // Add the app.
        $theapp = new \stdClass;
        $theapp->appname = $appname;
        $theapp->note = $note;
        $theapp->timemodified = time();

        $theapp->id = $DB->insert_record(constants::APP_TABLE, $theapp);
        $ret = $theapp->id;
        return $ret;
    }

    /**
     * Update app
     *
     * @param string $appname
     * @param string $note
     * @return bool
     */
    public static function update_app($appname, $note) {
        global $DB;

        $theapp = $DB->get_record(constants::APP_TABLE, ['appname' => $appname]);
        if (!$theapp) {
            return false;
        }

        // Build siteurl object.
        $theapp->note = $note;
        $theapp->timemodified = time();

        // Execute updaet and return.
        $ret = $DB->update_record(constants::APP_TABLE, $theapp);
        return $ret;
    }
}