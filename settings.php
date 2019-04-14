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
 * This is a class containing settings for the authplugin plugin
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Ensure the configurations for this site are set.
if ($hassiteconfig ) {

    // Create the new settings page.
    $settings = new admin_settingpage('local_ltsauthplugin', get_string('authpluginsettings', 'local_ltsauthplugin'));
    $ADMIN->add('localplugins', $settings );

    $ADMIN->add('root', new admin_category('ltsauthplugin', new lang_string('pluginname', 'local_ltsauthplugin')));
    $ADMIN->add('ltsauthplugin', new admin_externalpage('ltsauthplugin/authplugin_user',
        new lang_string('authplugin_user', 'local_ltsauthplugin'),
        new moodle_url('/local/ltsauthplugin/authplugin_user.php')));
    $ADMIN->add('ltsauthplugin', new admin_externalpage('ltsauthplugin/authplugin_subscription',
        new lang_string('authplugin_subscription', 'local_ltsauthplugin'),
        new moodle_url('/local/ltsauthplugin/authplugin_subscription.php')));
}
