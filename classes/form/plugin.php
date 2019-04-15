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
 * Class plugin
 *
 * @package     local_ltsauthplugin\form
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\form;

use core\form\persistent;

defined('MOODLE_INTERNAL') || die();

/**
 * Class plugin
 *
 * @package     local_ltsauthplugin\form
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin extends persistent {

    /** @var string The fully qualified classname. */
    protected static $persistentclass = \local_ltsauthplugin\persistent\plugin::class;

    /**
     * form definition
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Plugin name.
        $mform->addElement('text', 'name', get_string('ltspluginname', 'local_ltsauthplugin'));
        $mform->setType('name', PARAM_TEXT);
        if ($this->get_persistent()->get('id')) {
            $mform->freeze('name');
        } else {
            $mform->addRule('name', null, 'required', null, 'client');
        }

        // Notes.
        $mform->addElement('textarea', 'note', get_string('note', 'local_ltsauthplugin'));
        $mform->setType('note', PARAM_RAW);

        $this->add_action_buttons();
    }
}
