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
 * class usermanager
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\user;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/user/lib.php');

use \local_ltsauthplugin\constants;

/**
 * This is a class containing functions for sending authplugins
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class usermanager {
    /**
     * Check if CPAPI user exists
     *
     * @param int $userid
     * @return bool
     */
    public static function user_exists($userid) {
        global $DB;
        return $DB->record_exists(constants::USER_TABLE, array('userid' => $userid));
    }

    /**
     * Check if CPAPI user exists
     *
     * @param int $userid
     * @return mixed
     */
    public static function get_user($userid) {
        global $DB;
        return $DB->get_record(constants::USER_TABLE, array('userid' => $userid));
    }

    /**
     * Create a new CPAPI user
     *
     * @param int $resellerid
     * @param bool $userid
     * @param bool $awsaccessid
     * @param bool $awsaccesssecret
     * @return bool|int
     */
    public static function create_user($resellerid = 0, $userid = false, $awsaccessid = false, $awsaccesssecret = false) {
        global $DB, $USER;
        $ret = false;

        // If the userid was not passed in, then we use the current user.
        // This will be when added from webservice.
        if (!$userid) {
            $userid = $USER->id;
        }

        $theuser = new \stdClass;
        $theuser->userid = $userid;
        $theuser->resellerid = $resellerid;
        $theuser->timemodified = time();

        // Legacy and currently unused fields BEGIN.
        // Now store this info in seperate tables.
        $theuser->wildcardok = 0;
        $theuser->expiredate = 0;
        $theuser->transactionid = 0;
        $theuser->subscriptionid = 0;
        // Legacy unused fields END.

        // Awsaccess credentials are a Poodll specific thing.
        // You can remove them unless you are interested in assigning an Amazon IAM user to each of your clients.
        if ($awsaccessid) {
            $theuser->awsaccessid = $awsaccessid;
        }
        if ($awsaccesssecret) {
            $theuser->awsaccesssecret = $awsaccesssecret;
        }

        $theuser->id = $DB->insert_record(constants::USER_TABLE, $theuser);
        $ret = $theuser->id;

        return $ret;
    }

    /**
     * Update existing CPAPI user
     *
     * @param int $id
     * @param int $resellerid
     * @param bool $userid
     * @param bool $awsaccessid
     * @param bool $awsaccesssecret
     * @return bool
     */
    public static function update_user($id, $resellerid = 0, $userid = false,
                                       $awsaccessid = false, $awsaccesssecret = false) {
        global $DB;

        // It should not be possible to not pass in a userid here.
        if (!$userid) {
            return false;
        }

        $theuser = new \stdClass;
        $theuser->id = $id;
        $theuser->userid = $userid;
        $theuser->resellerid = $resellerid;
        $theuser->timemodified = time();

        // Legacy and currently unused fields BEGIN.
        $theuser->wildcardok = 0;
        $theuser->expiredate = 0;
        $theuser->transactionid = 0;
        $theuser->subscriptionid = 0;
        // Legacy unused fields END.

        // Awsaccess credentials are a Poodll specific thing.
        // You can remove them unless you are interested in assigning an Amazon IAM user to each of your clients.
        if ($awsaccessid) {
            $theuser->awsaccessid = $awsaccessid;
        }
        if ($awsaccesssecret) {
            $theuser->awsaccesssecret = $awsaccesssecret;
        }

        // Execute updaet and return.
        $ret = $DB->update_record(constants::USER_TABLE, $theuser);
        return $ret;
    }

    /**
     * Update existing Auth Plugin user, by username
     *
     * @param string $username
     * @param string $awsaccessid
     * @param string $awsaccesssecret
     * @return bool|int
     */
    public static function update_authpluginuser_by_username($username, $awsaccessid, $awsaccesssecret) {
        global $DB;
        $ret = false;

        $record = $DB->get_record_sql("SELECT authplugin.* FROM {" . constants::USER_TABLE . "
            } authplugin INNER JOIN {user} u ON u.id = authplugin.userid WHERE u.username = ?",
            array($username));
        if ($record) {
            $userid = $record->userid;
            $authpluginuserid = $record->id;
            $resellerid = $record->resellerid;
            $ret = self::update_user($authpluginuserid, $resellerid, $userid, $awsaccessid, $awsaccesssecret);
        } else {
            $moodleuser = $DB->get_record('user', array('username' => $username));
            if ($moodleuser) {
                $ret = self::create_user(0, $moodleuser->id, $awsaccessid, $awsaccesssecret);
            }
        }
        return $ret;
    }

    /**
     * Update standard user by username
     *
     * @param string $username
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @return bool
     */
    public static function update_standarduser_by_username($username, $firstname, $lastname, $email) {
        global $DB;
        $ret = false;

        $record = $DB->get_record('user', array('username' => $username));

        if ($record) {
            $user = new \stdClass();
            $user->id = $record->id;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->email = $email;
            user_update_user($user);
            $ret = true;
        }
        return $ret;
    }

    /**
     * Reset the user's secret (standard user)
     *
     * @param string $username
     * @param string $currentsecret
     * @return bool|string
     */
    public static function reset_user_secret($username, $currentsecret) {
        global $DB;
        $ret = false;

        $record = $DB->get_record('user', array('username' => $username));

        if ($record) {
            $newpassword = self::create_secret(16);
            $user = new \stdClass();
            $user->id = $record->id;
            $user->password = $newpassword;
            user_update_user($user);
            $ret = $newpassword;
        }
        return $ret;
    }

    /**
     * Create a new secret (standard user password)
     *
     * @param int $length
     * @return string
     */
    public static function create_secret($length) {
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces [] = $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    /**
     * Fetch the AWS creds for a user
     * @param int $moodleuserid
     */
    public static function fetch_awscreds($moodleuserid) {
        $ret = false;
        $user = self::get_user($moodleuserid);
        if ($user->awsaccessid && $user->awsaccesssecret) {
            $ret = new \stdClass();
            $ret->awsaccessid = $user->awsaccessid;
            $ret->awsaccesssecret = $user->awsaccesssecret;
        }
        return $ret;
    }
}