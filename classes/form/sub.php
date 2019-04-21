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
 * Class sub
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\form;

use local_ltsauthplugin\subscription\pluginmanager;

defined('MOODLE_INTERNAL') || die();

/**
 * Class sub
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sub extends \moodleform {

    /**
     * form definition
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHANUMEXT);
        $mform->setDefault('action', 'editsub');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Subscription name.
        $mform->addElement('text', 'name', get_string('subscriptionname', 'local_ltsauthplugin'));
        $mform->setType('name', PARAM_TEXT);

        // Add plugins selector.
        $options = $this->get_options_for_plugins();
        $mform->addElement('select', 'plugins', get_string('plugins', 'local_ltsauthplugin'),
            $options, ['multiple' => 1]);

        // Add note.
        $mform->addElement('textarea', 'note', get_string('note', 'local_ltsauthplugin'));
        $mform->setType('note', PARAM_RAW);

        $this->add_action_buttons();

        if (isset($this->_customdata['persistent']) &&
                ($this->_customdata['persistent'] instanceof \local_ltsauthplugin\persistent\sub)) {
            /** @var \local_ltsauthplugin\persistent\sub $sub */
            $sub = $this->_customdata['persistent'];
            $data = $sub->to_record();
            $data->plugins = $sub->get_plugins_list();
            $this->set_data($data);
        }
    }

    /**
     * set plugins field
     */
    protected function get_options_for_plugins() {
        global $OUTPUT;
        $plugins = pluginmanager::get_plugins_for_display();
        $options = [];
        foreach ($plugins as $p) {
            $data = $p->export($OUTPUT);
            $options[$data->name] = $data->name;
        }
        return $options;
    }

}
