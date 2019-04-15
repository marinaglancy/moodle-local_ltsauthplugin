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
 * class pluginmanager
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\subscription;

defined('MOODLE_INTERNAL') || die();

use local_ltsauthplugin\output\plugin_exporter;
use local_ltsauthplugin\persistent\plugin;

/**
 * This is a class containing functions for storing info about plugins
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pluginmanager {
    /**
     * Delete a plugin
     *
     * @param plugin $plugin
     */
    public static function delete(plugin $plugin) {
        $plugin->delete();
    }

    /**
     * Get plugins
     * @return plugin_exporter[]
     */
    public static function get_plugins_for_display() : array {
        return array_map(function(plugin $p) {
            return new plugin_exporter($p);
        }, plugin::get_records([], 'name'));
    }

    /**
     * Creates/updates a plugin
     *
     * @param plugin $theplugin
     * @param \stdClass $data
     * @throws \moodle_exception
     */
    public static function save(plugin $theplugin, \stdClass $data) {
        if ($theplugin->get('id')) {
            $theplugin->set('note', $data->note);
        } else if ($theplugin = plugin::find_by_name($data->name)) {
            throw new \moodle_exception('Plugin with this name already exists');
        } else {
            $theplugin = new plugin(0, (object)['name' => $data->name, 'note' => $data->note]);
        }
        $theplugin->save();
    }
}