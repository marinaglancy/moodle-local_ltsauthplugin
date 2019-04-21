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
    public function test_simple() {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/user/lib.php');

        $this->resetAfterTest();
        $plugin = new \local_ltsauthplugin\persistent\plugin(0, (object)['name' => 'p1']);
        $plugin->save();
        $sub = new \local_ltsauthplugin\persistent\sub(0, (object)['name' => 's1', 'plugins' => 'p1']);
        $sub->save();
        $u = new \local_ltsauthplugin\persistent\user(0, (object)['name' => 'u1']);
        $u->save();
        $usite = new \local_ltsauthplugin\persistent\user_site(0,
            (object)['url' => 'url1', 'ltsuserid' => $u->get('id')]);
        $usite->save();
        $usub = new \local_ltsauthplugin\persistent\user_sub(0,
            (object)['ltsuserid' => $u->get('id'), 'subscriptionid' => $sub->get('id')]);
        $usub->save();
    }
}
