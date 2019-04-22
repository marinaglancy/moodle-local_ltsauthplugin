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
 * Class user_site_exporter
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\output;

defined('MOODLE_INTERNAL') || die();

use core\external\persistent_exporter;
use local_ltsauthplugin\persistent\user_site;

/**
 * Class user_site_exporter
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_site_exporter extends persistent_exporter {

    /**
     * Defines the persistent class.
     *
     * @return string
     */
    protected static function define_class() {
        return user_site::class;
    }

    /**
     * Returns a list of objects that are related to this persistent.
     *
     * @return array of 'propertyname' => array('type' => classname, 'required' => true)
     */
    protected static function define_related() {
        return [
            'users' => user_exporter::class . '[]',
        ];
    }

    /**
     * other properties
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'username' => [
                'type' => PARAM_RAW
            ]
        ];
    }

    /**
     * other values
     * @param \renderer_base $output
     * @return array
     */
    protected function get_other_values(\renderer_base $output) {
        /** @var user_exporter[] $users */
        $users = $this->related['users'];
        $username = '?' . $this->data->ltsuserid;
        foreach ($users as $user) {
            if ($user->get_id() == $this->data->ltsuserid) {
                $username = $user->export($output)->name;
                break;
            }
        }
        return ['username' => $username];
    }

}
