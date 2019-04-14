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
 * This is a class containing functions for storing info about plugins
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class appmanager {
    /**
     * Delete an app
     *
     * @param string $pluginname
     */
    public static function delete_plugin($pluginname) {
        global $DB;
        return $DB->delete_records(constants::PLUGIN_TABLE, ['name' => $pluginname]);
    }

    /**
     * Get a  particular app
     *
     * @param string $pluginname
     */
    public static function get_plugin($pluginname) {
        global $DB;
        return $DB->get_record(constants::PLUGIN_TABLE, ['name' => $pluginname]);
    }

    /**
     * Get a  particular app
     */
    public static function get_plugins() {
        global $DB;
        return $DB->get_records(constants::PLUGIN_TABLE, array());
    }

    /**
     * Create a new app
     *
     * @param string $pluginname
     * @param string $note
     */
    public static function create_plugin($pluginname, $note) {
        global $DB;

        // Make sure we do not already have this app. And if so, just update it.
        $theplugin = $DB->get_record(constants::PLUGIN_TABLE, ['name' => $pluginname]);
        if ($theplugin) {
            throw new \moodle_exception('App with this name already exists');
        }

        // Add the app.
        $theplugin = new \stdClass;
        $theplugin->name = $pluginname;
        $theplugin->note = $note;
        $theplugin->timemodified = time();

        $theplugin->id = $DB->insert_record(constants::PLUGIN_TABLE, $theplugin);
        $ret = $theplugin->id;
        return $ret;
    }

    /**
     * Update app
     *
     * @param string $appname
     * @param string $note
     * @return bool
     */
    public static function update_plugin($appname, $note) {
        global $DB;

        $theplugin = $DB->get_record(constants::PLUGIN_TABLE, ['name' => $appname]);
        if (!$theplugin) {
            return false;
        }

        // Build siteurl object.
        $theplugin->note = $note;
        $theplugin->timemodified = time();

        // Execute updaet and return.
        $ret = $DB->update_record(constants::PLUGIN_TABLE, $theplugin);
        return $ret;
    }
}