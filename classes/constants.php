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
 * class constants
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin;

defined('MOODLE_INTERNAL') || die();

/**
 * Constants
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class constants {
    /** @var string */
    const USERSITE_TABLE = 'local_ltsauthplugin_usersite';
    /** @var string */
    const USER_TABLE = 'local_ltsauthplugin_users';
    /** @var string */
    const SUB_TABLE = 'local_ltsauthplugin_subs';
    /** @var string */
    const APP_TABLE = 'local_ltsauthplugin_apps';
    /** @var string */
    const USERSUB_TABLE = 'local_ltsauthplugin_usersubs';
    /** @var string */
    const AWSACCESSID_NONE = 'noaccessid';
    /** @var string */
    const AWSACCESSSECRET_NONE = 'noaccesssecret';
}