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
 * File containing tests for local_ltsauthplugin
 *
 * @package     local_ltsauthplugin
 * @category    test
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for the local_ltsauthplugin
 *
 * @package    local_ltsauthplugin
 * @copyright  2019 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_ltsauthplugin_plugin_testcase extends advanced_testcase {

    /**
     * Test for user created observer
     */
    public function test_observer() {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/user/lib.php');

        $this->resetAfterTest();

        $user = array(
            'username' => 'usernametest1',
            'password' => 'Moodle2012!',
            'idnumber' => 'idnumbertest1',
            'firstname' => 'First Name User Test 1',
            'lastname' => 'Last Name User Test 1',
            'middlename' => 'Middle Name User Test 1',
            'lastnamephonetic' => '最後のお名前のテスト一号',
            'firstnamephonetic' => 'お名前のテスト一号',
            'alternatename' => 'Alternate Name User Test 1',
            'email' => 'usertest1@example.com',
            'description' => 'This is a description for user 1',
            'city' => 'Perth',
            'country' => 'AU'
        );

        $userid = user_create_user((object)$user);

        $authuser = $DB->get_record(\local_ltsauthplugin\constants::USER_TABLE, ['userid' => $userid]);
        $this->assertEquals($userid, $authuser->userid);
    }
}
