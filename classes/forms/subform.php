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

class subform extends baseform {
    public function custom_definition() {
        // Add wild card.
        global $DB;

        // Subscription id.
        $this->_form->addElement('text', 'subscriptionid', get_string('subscriptionid', 'local_ltsauthplugin'));
        $this->_form->setType('subscriptionid', PARAM_INT);
        $this->_form->setDefault('subscriptionid', 0);
        // Transaction id.
        $this->_form->addElement('text', 'subscriptionname', get_string('subscriptionname', 'local_ltsauthplugin'));
        $this->_form->setType('subscriptionname', PARAM_TEXT);
        $this->_form->setDefault('subscriptionname', '');

        // Add apps selector.
        $this->set_apps_field('apps', get_string('apps', 'local_ltsauthplugin'));

        // Add wild card.
        $this->_form->addElement('selectyesno', 'wildcard', get_string('wildcard', 'local_ltsauthplugin'));
        $this->_form->setDefault('wildcard', 0);
    }
}