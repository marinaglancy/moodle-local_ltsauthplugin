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
 * Class log_plugin
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\persistent;

defined('MOODLE_INTERNAL') || die();

use core\persistent;

/**
 * Class log_plugin
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class log_plugin extends persistent {

    /** @var string Table name this persistent is mapped to. */
    const TABLE = 'local_ltsauthplugin_lgplugin';

    /**
     * Properties definitions
     *
     * @return array
     */
    public static function define_properties() {
        return [
            'logid' => [
                'type' => PARAM_INT,
            ],
            'pluginname' => [
                'type' => PARAM_RAW,
            ],
            'pluginversion' => [
                'type' => PARAM_RAW,
                'default' => '',
            ],
            'status' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'pluginid' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
        ];
    }
}
