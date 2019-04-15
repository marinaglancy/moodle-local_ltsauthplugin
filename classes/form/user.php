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
 * Class user
 *
 * @package     local_ltsauthplugin\form
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\form;

use core\form\persistent;

defined('MOODLE_INTERNAL') || die();

/**
 * Class user
 *
 * @package     local_ltsauthplugin\form
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user extends persistent {

    /** @var string The fully qualified classname. */
    protected static $persistentclass = \local_ltsauthplugin\persistent\user::class;

    /**
     * form definition
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Add user selector.
        $options = $this->get_user_id_options();
        $mform->addElement('select', 'userid', get_string('user'), $options);

        // Note.
        $mform->addElement('textarea', 'note', get_string('note', 'local_ltsauthplugin'));
        $mform->setType('note', PARAM_RAW);
        $mform->setDefault('note', '');

        $this->add_action_buttons();
    }

    /**
     * set user field
     */
    protected function get_user_id_options() {
        global $DB;
        return $DB->get_records_sql_menu('SELECT id,username FROM {user}', array());
    }
}
