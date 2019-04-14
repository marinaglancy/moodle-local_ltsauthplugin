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
     * @param int $ltsuserid
     * @param int $subscriptionid
     * @return bool
     */
    public static function delete_usersub($ltsuserid, $subscriptionid) {
        global $DB;
        $ret = $DB->delete_records(constants::USERSUB_TABLE, array('ltsuserid' => $ltsuserid, 'subscriptionid' => $subscriptionid));
        return $ret;
    }

    /**
     * Create a  particular user subscriptions
     *
     * @param int $ltsuserid
     * @param int $subscriptionid
     * @return \stdClass
     */
    public static function get_usersub($ltsuserid, $subscriptionid) {
        global $DB;
        $sub = $DB->get_record(constants::USERSUB_TABLE, array('ltsuserid' => $ltsuserid, 'subscriptionid' => $subscriptionid));
        return $sub;
    }

    /**
     * Create a  particular user subscriptions
     * @param int $ltsuserid
     * @return array
     */
    public static function get_usersubs_plugins($ltsuserid) {
        global $DB;

        $subs = $DB->get_records_sql('SELECT usb.*,subs.name,subs.plugins FROM {'
            . constants::USERSUB_TABLE . '} usb'
            . ' INNER JOIN {' . constants::SUB_TABLE . '} subs ON usb.subscriptionid = subs.id'
            . ' WHERE ltsuserid = ?'
            , array($ltsuserid));

        return $subs;
    }

    /**
     * Create a users subscriptions
     *
     * @param int $ltsuserid
     * @return array|false
     */
    public static function get_usersubs($ltsuserid) {
        global $DB;
        $ret = false;

        $subs = $DB->get_records(constants::USERSUB_TABLE, array('ltsuserid' => $ltsuserid));
        if ($subs) {
            return $subs;
        }

        return $ret;
    }

    /**
     * get usersubs for display
     *
     * @param int $ltsuserid
     * @return array|bool
     */
    public static function get_usersubs_fordisplay($ltsuserid) {
        global $DB;
        $ret = false;

        $subs = $DB->get_records_sql('SELECT usb.*,subs.name, u.username '
            . ' FROM {' . constants::USERSUB_TABLE . '} usb '
            . ' INNER JOIN {' . constants::SUB_TABLE . '} subs ON usb.subscriptionid = subs.id'
            . ' INNER JOIN {' . constants::USER_TABLE . '} ut ON ut.id = usb.ltsuserid'
            . ' INNER JOIN {user} u ON u.id = ut.userid '
            . ' WHERE usb.ltsuserid = ?'
            , array($ltsuserid));

        if ($subs) {
            return $subs;
        }

        return $ret;
    }

    /**
     * Create a new usersubscription
     *
     * @param int $subid
     * @param string $note
     * @param int $expiredate
     * @param int $ltsuserid
     * @return bool|int
     */
    public static function create_usersub($subid, $note, $expiredate, $ltsuserid) {
        global $DB, $USER;

        // Lets not be creating multiple of the same sub per user. this would be just bad.
        $theusersub = self::get_usersub($ltsuserid, $subid);
        if ($theusersub) {
            return self::update_usersub($subid, $note, $expiredate, $ltsuserid);
        }

        $theusersub = new \stdClass;
        $theusersub->ltsuserid = $ltsuserid;
        $theusersub->subscriptionid = $subid;
        $theusersub->note = $note;
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
     * @param string $note
     * @param int $expiredate
     * @param int $ltsuserid
     * @return bool
     */
    public static function update_usersub($subscriptionid, $note, $expiredate, $ltsuserid) {
        global $DB;

        // It should not be possible to not pass in a userid here.
        if (!$ltsuserid) {
            return false;
        }

        $thesub = self::get_usersub($ltsuserid, $subscriptionid);
        $thesub->note = $note;
        $thesub->expiredate = $expiredate;
        $thesub->timemodified = time();

        // Execute updaet and return.
        $ret = $DB->update_record(constants::USERSUB_TABLE, $thesub);
        return $ret;
    }
}