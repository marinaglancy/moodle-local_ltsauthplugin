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

class usersiteform extends baseform {
    public function custom_definition() {
        $this->_form->addElement('hidden', 'userid');
        $this->_form->setType('userid', PARAM_INT);

        $this->_form->addElement('text', 'url1', get_string('site', 'local_ltsauthplugin'));
        $this->_form->setType('url1', PARAM_TEXT);
    }
}