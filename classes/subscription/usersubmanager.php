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

use \local_ltsauthplugin\constants;

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
     * @param int $userid
     * @param int $subscriptionid
     * @return bool
     */
    public static function delete_usersub($userid, $subscriptionid) {
        global $DB;
        $ret = $DB->delete_records(constants::USERSUB_TABLE, array('userid' => $userid, 'subscriptionid' => $subscriptionid));
        return $ret;
    }

    /**
     * Create a  particular user subscriptions
     *
     * @param int $userid
     * @param int $subscriptionid
     * @return \stdClass
     */
    public static function get_usersub($userid, $subscriptionid) {
        global $DB;
        $sub = $DB->get_record(constants::USERSUB_TABLE, array('userid' => $userid, 'subscriptionid' => $subscriptionid));
        return $sub;
    }

    /**
     * Create a  particular user subscriptions
     * @param int $userid
     * @return array
     */
    public static function get_usersubs_apps($userid) {
        global $DB;

        $subs = $DB->get_records_sql('SELECT usb.*,subs.subscriptionname,subs.apps, subs.wildcard FROM {'
            . constants::USERSUB_TABLE . '} usb'
            . ' INNER JOIN {' . constants::SUB_TABLE . '} subs ON usb.subscriptionid = subs.subscriptionid'
            . ' WHERE userid = ?'
            , array($userid));

        return $subs;
    }

    /**
     * Create a users subscriptions
     * @param int $userid
     * @return array|false
     */
    public static function get_usersubs($userid) {
        global $DB;
        $ret = false;

        $subs = $DB->get_records(constants::USERSUB_TABLE, array('userid' => $userid));
        if ($subs) {
            return $subs;
        }

        return $ret;
    }

    /**
     * get usersubs for display
     *
     * @param int $userid
     * @return array|bool
     */
    public static function get_usersubs_fordisplay($userid) {
        global $DB;
        $ret = false;

        $subs = $DB->get_records_sql('SELECT usb.*,subs.subscriptionname, u.username FROM {'
            . constants::USERSUB_TABLE . '} usb INNER JOIN {user} u ON u.id = usb.userid '
            . ' INNER JOIN {' . constants::SUB_TABLE . '} subs ON usb.subscriptionid = subs.subscriptionid'
            . ' WHERE userid = ?'
            , array($userid));

        if ($subs) {
            return $subs;
        }

        return $ret;
    }

    /**
     * Create a users subscriptions
     *
     * @param int $userid
     * @param int $subscriptionid
     * @return \stdClass|false
     */
    public static function get_usersub_by_subscriptionid($userid, $subscriptionid) {
        global $DB;
        $ret = false;

        $sub = $DB->get_record(constants::USERSUB_TABLE, array('userid' => $userid, 'subscriptionid' => $subscriptionid));
        if ($sub) {
            return $sub;
        }
        return $ret;
    }

    /**
     * get auth plugin user by username
     * @param string $username
     * @return mixed
     */
    public static function get_authpluginuser_by_username($username) {
        global $DB;

        $authpluginuser = $DB->get_record_sql("SELECT authplugin.* FROM {" . constants::USER_TABLE .
            "} authplugin INNER JOIN {user} u ON u.id = authplugin.userid WHERE u.username = ?;", array($username));
        return $authpluginuser;
    }

    /**
     * Create a users subscriptions
     *
     * @param string $username
     */
    public static function get_usersubs_by_username($username) {
        $ret = false;

        $authpluginuser = self::get_authpluginuser_by_username($username);
        if ($authpluginuser) {
            $ret = self::get_usersubs($authpluginuser->userid);
        }
        return $ret;
    }

    /**
     * Create a new usersubscription
     *
     * @param int $subid
     * @param int $transid
     * @param int $expiredate
     * @param bool $userid
     * @return bool|int
     */
    public static function create_usersub($subid = 0, $transid = 0, $expiredate = 0, $userid = false) {
        global $DB, $USER;

        // If the userid was not passed in, then we use the current user
        // this will be when added from webservice.
        if (!$userid) {
            $userid = $USER->id;
        }

        // Lets not be creating multiple of the same sub per user. this would be just bad.
        $theusersub = self::get_usersub_by_subscriptionid($userid, $subid);
        if ($theusersub) {
            return self::update_usersub($subid, $transid, $expiredate, $userid);
        }

        $theusersub = new \stdClass;
        $theusersub->userid = $userid;
        $theusersub->subscriptionid = $subid;
        $theusersub->transactionid = $transid;
        $theusersub->expiredate = $expiredate;
        $theusersub->timemodified = time();

        $theusersub->id = $DB->insert_record(constants::USERSUB_TABLE, $theusersub);
        $ret = $theusersub->id;

        return $ret;
    }

    /**
     * update user sub
     *
     * @param int $subscriptionid
     * @param int $transactionid
     * @param int $expiredate
     * @param int $userid
     * @return bool
     */
    public static function update_usersub($subscriptionid, $transactionid, $expiredate, $userid) {
        global $DB;

        // It should not be possible to not pass in a userid here.
        if (!$userid) {
            return false;
        }

        $thesub = self::get_usersub_by_subscriptionid($userid, $subscriptionid);
        $thesub->transactionid = $transactionid;
        $thesub->expiredate = $expiredate;
        $thesub->timemodified = time();

        // Execute updaet and return.
        $ret = $DB->update_record(constants::USERSUB_TABLE, $thesub);
        return $ret;
    }
}