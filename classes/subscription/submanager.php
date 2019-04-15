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

use local_ltsauthplugin\output\sub_exporter;
use local_ltsauthplugin\persistent\sub;

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
     * @param sub $subscription
      */
    public static function delete(sub $subscription) {
        $subscription->delete();
    }

    /**
     * save subscription
     * @param sub $subscription
     * @param \stdClass $data
     */
    public static function save(sub $subscription, \stdClass $data) {
        $subscription->set('name', $data->name);
        $subscription->set('plugins', join(',', $data->plugins));
        $subscription->set('note', $data->note);
        $subscription->save();
    }

    /**
     * prepare for set_data
     * @param sub $subscription
     * @return \stdClass
     */
    public static function prepare_data_for_form(sub $subscription) {
        $data = $subscription->to_record();
        $data->plugins = preg_split('/,/', $data->plugins, -1, PREG_SPLIT_NO_EMPTY);
        return $data;
    }

    /**
     * Get a  particular subscription
     * @return sub_exporter[]
     */
    public static function get_subs_for_display(): array {
        return array_map(function(sub $p) {
            return new sub_exporter($p);
        }, sub::get_records([], 'name'));
    }

}