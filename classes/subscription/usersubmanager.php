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
 * class usersubmanager
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\subscription;

defined('MOODLE_INTERNAL') || die();

use local_ltsauthplugin\output\user_exporter;
use local_ltsauthplugin\output\user_sub_exporter;
use local_ltsauthplugin\persistent\sub;
use local_ltsauthplugin\persistent\user;
use local_ltsauthplugin\persistent\user_sub;
use local_ltsauthplugin\user\usermanager;

/**
 * This is a class containing functions for sending authplugins
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class usersubmanager {

    /**
     * Delete a usersubscription
     *
     * @param user_sub $sub
     */
    public static function delete(user_sub $sub) {
        $sub->delete();
    }

    /**
     * get usersubs for display
     *
     * @param user $user
     * @return user_sub_exporter[]
     */
    public static function get_user_subs_for_display(user $user) {
        $subs = submanager::get_subs_for_display();
        $related = ['subs' => $subs, 'users' => [new user_exporter($user)]];
        return array_map(function(user_sub $us) use ($related) {
            return new user_sub_exporter($us, $related);
        }, user_sub::get_records(['ltsuserid' => $user->get('id')], 'subscriptionid'));
    }

    /**
     * Creates/updates a user sub
     *
     * @param user_sub $sub
     * @param \stdClass $data
     */
    public static function save(user_sub $sub, \stdClass $data) {
        if (!$sub->get('id')) {
            $sub->set('subscriptionid', $data->subscriptionid);
        }
        $sub->set('note', $data->note);
        $sub->set('expiredate', $data->expiredate);
        $sub->save();
    }

    /**
     * get subscriptions that recently expired
     * @param int $period
     * @return user_sub_exporter[]
     */
    public static function get_expired(int $period) {
        $usersubs = user_sub::get_records_select('expiredate < :now AND expiredate > :start',
            ['now' => time(), 'start' => time() - $period], 'expiredate DESC');
        if (!$usersubs) {
            return [];
        }
        $subs = submanager::get_subs_for_display();
        $users = usermanager::get_users_for_display();
        $related = ['subs' => $subs, 'users' => $users];
        return array_map(function(user_sub $us) use ($related) {
            return new user_sub_exporter($us, $related);
        }, $usersubs);
    }
}