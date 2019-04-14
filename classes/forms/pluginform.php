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
 * class pluginform
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\forms;

defined('MOODLE_INTERNAL') || die();

/**
 * Class pluginform
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pluginform extends baseform {

    /**
     * Custom definition
     */
    public function custom_definition() {
        // Plugin name.
        $this->_form->addElement('text', 'name', get_string('ltspluginname', 'local_ltsauthplugin'));
        $this->_form->setType('name', PARAM_TEXT);
        $this->_form->setDefault('name', '');
        // Notes.
        $this->_form->addElement('textarea', 'note', get_string('note', 'local_ltsauthplugin'));
        $this->_form->setType('note', PARAM_RAW);
        $this->_form->setDefault('note', '');
    }
}
