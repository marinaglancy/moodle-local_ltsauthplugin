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
 * Class user_site
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\form;

use core\form\persistent;

defined('MOODLE_INTERNAL') || die();

/**
 * Class user_site
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_site extends persistent {

    /** @var string The fully qualified classname. */
    protected static $persistentclass = \local_ltsauthplugin\persistent\user_site::class;

    /** @var array Fields to remove from the persistent validation. */
    protected static $foreignfields = ['action'];


    /**
     * form definition
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHANUMEXT);
        $mform->setDefault('action', 'editusersite');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'ltsuserid');
        $mform->setType('ltsuserid', PARAM_INT);

        $mform->addElement('text', 'url', get_string('site', 'local_ltsauthplugin'));
        $mform->setType('url', PARAM_TEXT);
        $mform->addRule('url', null, 'required', null, 'client');

        // Add note.
        $mform->addElement('textarea', 'note', get_string('note', 'local_ltsauthplugin'));
        $mform->setType('note', PARAM_RAW);

        $this->add_action_buttons();
    }

    /**
     * Extra validation
     * @param \stdClass $data
     * @param array $files
     * @param array $errors
     * @return array|void
     */
    public function extra_validation($data, $files, array &$errors) {
        parent::extra_validation($data, $files, $errors);
        $sites = \local_ltsauthplugin\persistent\user_site::get_records_select('url = :url AND id <> :id',
            ['url' => $data->url, 'id' => $data->id]);
        if ($sites) {
            $errors['url'] = 'This URL is already registered for the same or another user'; // TODO string.
        }
    }

}
