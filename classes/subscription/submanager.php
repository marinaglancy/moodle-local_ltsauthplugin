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
 * class submanager
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\subscription;

defined('MOODLE_INTERNAL') || die();

use local_ltsauthplugin\persistent\user_sub;
use local_ltsauthplugin\output\sub_exporter;
use local_ltsauthplugin\persistent\sub;

/**
 * This is a class containing functions for storing info about subs
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submanager {

    /**
     * Delete sub
     * @param sub $subscription
     */
    public static function delete(sub $subscription) {
        /** @var user_sub[] $subs */
        $subs = user_sub::get_records(['subscriptionid' => $subscription->get('id')]);
        foreach ($subs as $sub) {
            usersubmanager::delete($sub);
        }
        $subscription->delete();
    }

    /**
     * save subscription
     * @param sub $subscription
     * @param \stdClass $data
     */
    public static function save(sub $subscription, \stdClass $data) {
        $subscription->set('name', $data->name);
        $subscription->set('plugins', $data->plugins);
        $subscription->set('note', $data->note);
        $subscription->save();
    }

    /**
     * Get a  particular subscription
     * @return sub_exporter[]
     */
    public static function get_subs_for_display(): array {
        return array_map(function(sub $p) {
            return new sub_exporter($p);
        }, sub::get_records([], 'name'));
    }

}