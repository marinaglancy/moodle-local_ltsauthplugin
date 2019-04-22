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

use local_ltsauthplugin\output\user_exporter;
use local_ltsauthplugin\output\user_site_exporter;
use local_ltsauthplugin\persistent\log;
use local_ltsauthplugin\persistent\user;
use local_ltsauthplugin\persistent\user_site;
use local_ltsauthplugin\persistent\user_sub;

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
     * @param user $user
     * @return user_site_exporter[]
     */
    public static function get_user_sites_for_display(user $user) {
        $related = ['users' => [new user_exporter($user)]];
        return array_map(function(user_site $p) use ($related) {
            return new user_site_exporter($p, $related);
        }, user_site::get_records(['ltsuserid' => $user->get('id')], 'url'));
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

    /**
     * get_user_sites_without_logs
     * @param int $period
     * @return user_site_exporter[]
     */
    public static function get_user_sites_without_logs(int $period) {
        global $DB;

        $sql = "SELECT us.*
            FROM {".user_site::TABLE."} us
            LEFT JOIN {".log::TABLE."} l ON l.ltsuserid = us.ltsuserid AND l.url = us.url AND l.timecreated > :start
            WHERE
            EXISTS (SELECT 1 FROM {".user_sub::TABLE."} usub WHERE usub.ltsuserid = us.ltsuserid AND usub.expiredate > :now)
            AND l.id IS NULL
            ORDER BY us.url";
        $params = ['now' => time(), 'start' => time() - $period];

        $records = $DB->get_records_sql($sql, $params);
        if (!$records) {
            return [];
        }

        $users = usermanager::get_users_for_display();
        $related = ['users' => $users];

        $instances = array();
        foreach ($records as $key => $record) {
            $instances[] = new user_site_exporter(new user_site(0, $record), $related);
        }
        return $instances;
    }
}