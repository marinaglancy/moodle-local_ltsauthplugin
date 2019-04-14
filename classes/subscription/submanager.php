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
 * class submanager
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\subscription;

defined('MOODLE_INTERNAL') || die();

use \local_ltsauthplugin\constants;

/**
 * This is a class containing functions for storing info about subs
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submanager {

    /**
     * Delete sub
     * @param int $subscriptionid
     * @return array
     */
    public static function delete_sub($subscriptionid) {
        global $DB;
        $ret = $DB->delete_records(constants::SUB_TABLE, array('id' => $subscriptionid));
        return $ret;
    }

    /**
     * Get a  particular subscription
     * @param int $subscriptionid
     * @return array
     */
    public static function get_sub($subscriptionid) {
        global $DB;
        $sub = $DB->get_record(constants::SUB_TABLE, array('id' => $subscriptionid));
        return $sub;
    }

    /**
     * Get a  particular subscription
     * @return array
     */
    public static function get_subs() {
        global $DB;
        $sub = $DB->get_records(constants::SUB_TABLE, array());
        return $sub;
    }

    /**
     * Create a new subscription
     *
     * @param int $subid
     * @param string $subname
     * @param mixed $plugins
     * @param mixed $note
     * @return bool|int
     */
    public static function create_sub($subid, $subname, $plugins, $note) {
        global $DB;

        // Add the sub.
        $thesub = new \stdClass;
        $thesub->name = $subname;
        $thesub->plugins = $plugins;
        $thesub->note = $note;
        $thesub->timemodified = time();

        $thesub->id = $DB->insert_record(constants::SUB_TABLE, $thesub);
        $ret = $thesub->id;
        return $ret;
    }

    /**
     * update sub
     *
     * @param int $subid
     * @param string $subname
     * @param string $plugins
     * @param string $note
     * @return bool
     */
    public static function update_sub($subid, $subname, $plugins, $note) {
        global $DB;

        $thesub = $DB->get_record(constants::SUB_TABLE, array('id' => $subid));
        if (!$thesub) {
            return false;
        }

        // Build siteurl object.
        $thesub->name = $subname;
        $thesub->plugins = $plugins;
        $thesub->note = $note;
        $thesub->timemodified = time();

        // Execute updaet and return.
        $ret = $DB->update_record(constants::SUB_TABLE, $thesub);
        return $ret;
    }

}