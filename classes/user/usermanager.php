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

use local_ltsauthplugin\persistent\user;
use local_ltsauthplugin\persistent\user_site;
use local_ltsauthplugin\persistent\user_sub;
use local_ltsauthplugin\subscription\usersubmanager;

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
     * Creates/updates user
     * @param user $ltsuser
     * @param \stdClass|null $data
     */
    public static function save(user $ltsuser, \stdClass $data = null) {
        if (isset($data->name)) {
            $ltsuser->set('name', $data->name);
        }
        if (isset($data->note)) {
            $ltsuser->set('note', $data->note);
        }
        $ltsuser->save();
    }

    /**
     * Delete a user
     * @param user $ltsuser
     */
    public static function delete(user $ltsuser) {
        /** @var user_sub[] $subs */
        $subs = user_sub::get_records(['ltsuserid' => $ltsuser->get('id')]);
        foreach ($subs as $sub) {
            usersubmanager::delete($sub);
        }
        /** @var user_site[] $sites */
        $sites = user_site::get_records(['ltsuserid' => $ltsuser->get('id')]);
        foreach ($sites as $site) {
            usersitemanager::delete($site);
        }
        $ltsuser->delete();
    }
}