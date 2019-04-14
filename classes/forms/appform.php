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
 * class appform
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\forms;

defined('MOODLE_INTERNAL') || die();

/**
 * Class appform
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class appform extends baseform {

    /**
     * Custom definition
     */
    public function custom_definition() {
        // Subscription id.
        $this->_form->addElement('text', 'appid', get_string('appid', 'local_ltsauthplugin'));
        $this->_form->setType('appid', PARAM_TEXT);
        $this->_form->setDefault('appid', '');
        // Transaction id.
        $this->_form->addElement('text', 'appname', get_string('appname', 'local_ltsauthplugin'));
        $this->_form->setType('appname', PARAM_TEXT);
        $this->_form->setDefault('appname', '');
    }
}
