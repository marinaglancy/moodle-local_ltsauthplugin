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

/**
 * Class userform
 *
 * @package local_ltsauthplugin\forms
 */
class userform extends baseform {

    /**
     * Custom definition
     */
    public function custom_definition() {
        // Add user selector.
        $this->setUsersField('userid', get_string('user'));

        // Reseller id.
        $this->_form->addElement('text', 'resellerid', get_string('resellerid', 'local_ltsauthplugin'));
        $this->_form->setType('resellerid', PARAM_INT);
        $this->_form->setDefault('resellerid', 0);

        // AWS Access ID.
        $this->_form->addElement('text', 'awsaccessid', get_string('awsaccessid', 'local_ltsauthplugin'));
        $this->_form->setType('awsaccessid', PARAM_TEXT);
        $this->_form->setDefault('awsaccessid', '');

        // AWS Access Secret.
        $this->_form->addElement('text', 'awsaccesssecret', get_string('awsaccesssecret', 'local_ltsauthplugin'));
        $this->_form->setType('awsaccesssecret', PARAM_TEXT);
        $this->_form->setDefault('awsaccesssecret', '');
    }
}