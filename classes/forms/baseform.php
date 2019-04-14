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

namespace local_ltsauthplugin\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Abstract class that item type's inherit from.
 *
 * This is the abstract class that add item type forms must extend.
 *
 * @abstract
 * @copyright  2018 Justin Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class baseform extends \moodleform {
    /**
     * Custom definition
     *
     * Each item type can and should override this to add any custom elements to
     * the basic form that they want
     */
    public function custom_definition() {
    }

    public function setUsersField($fieldname, $fieldlabel) {
        global $DB;
        $users = $DB->get_records_sql_menu('SELECT id,username FROM {user}', array());
        $this->_form->addElement('select', $fieldname, $fieldlabel, $users);
        $this->_form->setType($fieldname, PARAM_INT);
    }

    public function setSubsField($fieldname, $fieldlabel) {
        global $DB;
        $subs = $DB->get_records_sql_menu('SELECT subscriptionid, subscriptionname FROM {local_ltsauthplugin_subs}', array());
        $this->_form->addElement('select', $fieldname, $fieldlabel, $subs);
        $this->_form->setType($fieldname, PARAM_INT);
    }

    public function setAppsField($fieldname, $fieldlabel) {
        global $DB;
        $apps = $DB->get_records_sql_menu('SELECT appid, appname FROM {local_ltsauthplugin_apps}', array());
        $select = $this->_form->addElement('select', $fieldname, $fieldlabel, $apps);
        $this->_form->setType($fieldname, PARAM_TEXT);
        $select->setMultiple($fieldname, PARAM_TEXT);
    }

    /**
     * Add the required basic elements to the form.
     *
     * This method adds the basic elements to the form including title and contents
     * and then calls custom_definition();
     */
    public final function definition() {
        global $DB;
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->custom_definition();

        // Add the action buttons.
        $this->add_action_buttons(get_string('cancel'), get_string('saveitem', 'local_ltsauthplugin'));
    }

    /**
     * A function that gets called upon init of this object by the calling script.
     *
     * This can be used to process an immediate action if required. Currently it
     * is only used in special cases by non-standard item types.
     *
     * @return bool
     */
    public function construction_override($itemid, $authplugin) {
        return true;
    }
}