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
 * Class user_sub
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\persistent;

defined('MOODLE_INTERNAL') || die();

use core\persistent;

/**
 * Class user_sub
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_sub extends persistent {

    /** @var string Table name this persistent is mapped to. */
    const TABLE = 'local_ltsauthplugin_usersub';

    /**
     * Properties definitions
     *
     * @return array
     */
    public static function define_properties() {
        return [
            'ltsuserid' => [
                'type' => PARAM_INT,
            ],
            'subscriptionid' => [
                'type' => PARAM_INT,
            ],
            'expiredate' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'note' => [
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
        ];
    }

    /**
     * Is subscription expired?
     * @return bool
     */
    public function is_expired() {
        return $this->get('expiredate') && $this->get('expiredate') < time();
    }
}
