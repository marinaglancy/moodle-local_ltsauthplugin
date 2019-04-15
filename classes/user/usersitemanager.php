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

use local_ltsauthplugin\output\user_site_exporter;
use local_ltsauthplugin\persistent\user_site;

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
     * @param user_site $usersite
     */
    public static function delete(user_site $usersite) {
        $usersite->delete();
    }

    /**
     * Get a list of user sites ready for display
     *
     * @param int $ltsuserid
     * @return user_site_exporter[]
     */
    public static function get_user_sites_for_display($ltsuserid) {
        return array_map(function(user_site $p) {
            return new user_site_exporter($p);
        }, user_site::get_records(['ltsuserid' => $ltsuserid], 'url'));
    }

    /**
     * Create/update user site
     * @param user_site $usersite
     * @param \stdClass $data
     */
    public static function save(user_site $usersite, \stdClass $data) {
        $usersite->set('url', $data->url);
        $usersite->set('note', $data->note);
        $usersite->save();
    }
}