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
 * Class user_sub_exporter
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\output;

use core\external\persistent_exporter;
use local_ltsauthplugin\persistent\sub;
use local_ltsauthplugin\persistent\user_sub;

defined('MOODLE_INTERNAL') || die();

/**
 * Class user_sub_exporter
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_sub_exporter extends persistent_exporter {

    /**
     * Defines the persistent class.
     *
     * @return string
     */
    protected static function define_class() {
        return user_sub::class;
    }

    /**
     * Returns a list of objects that are related to this persistent.
     *
     * Only objects listed here can be cached in this object.
     *
     * The class name can be suffixed:
     * - with [] to indicate an array of values.
     * - with ? to indicate that 'null' is allowed.
     *
     * @return array of 'propertyname' => array('type' => classname, 'required' => true)
     */
    protected static function define_related() {
        return [
            'subs' => sub_exporter::class . '[]',
        ];
    }

    /**
     * other properties
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'name' => [
                'type' => PARAM_RAW
            ],
        ];
    }

    /**
     * other values
     * @param \renderer_base $output
     * @return array
     */
    protected function get_other_values(\renderer_base $output) {
        /** @var sub_exporter[] $subs */
        $subs = $this->related['subs'];
        $name = '?' . $this->data->subscriptionid;
        foreach ($subs as $sub) {
            if ($sub->get_id() == $this->data->subscriptionid) {
                $name = $sub->export($output)->name;
                break;
            }
        }
        return ['name' => $name];
    }
}
