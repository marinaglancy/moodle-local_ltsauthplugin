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

use local_ltsauthplugin\output\user_sub_exporter;
use local_ltsauthplugin\persistent\user_sub;

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
     * @param int $ltsuserid
     * @return user_sub_exporter[]
     */
    public static function get_user_subs_for_display($ltsuserid) {
        $subs = submanager::get_subs_for_display();
        return array_map(function(user_sub $us) use ($subs) {
            return new user_sub_exporter($us, ['subs' => $subs]);
        }, user_sub::get_records(['ltsuserid' => $ltsuserid], 'subscriptionid'));
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
}