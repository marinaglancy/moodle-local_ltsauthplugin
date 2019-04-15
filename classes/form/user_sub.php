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
 * Class user_sub
 *
 * @package     local_ltsauthplugin\form
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\form;

use core\form\persistent;
use local_ltsauthplugin\subscription\submanager;

defined('MOODLE_INTERNAL') || die();

/**
 * Class user_sub
 *
 * @package     local_ltsauthplugin\form
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_sub extends persistent {

    /** @var string The fully qualified classname. */
    protected static $persistentclass = \local_ltsauthplugin\persistent\user_sub::class;

    /**
     * form definition
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->_form->addElement('hidden', 'ltsuserid');
        $this->_form->setType('ltsuserid', PARAM_INT);

        // Add subscriptions selector.
        $options = $this->get_options_for_sub();
        $this->_form->addElement('select', 'subscriptionid', get_string('subscription', 'local_ltsauthplugin'), $options);

        // Expiredate.
        $dateopts = array(
            'startyear' => 2018,
            'stopyear' => 2030,
            'timezone' => 99,
            'optional' => false
        );
        $this->_form->addElement('date_selector', 'expiredate', get_string('expiredate', 'local_ltsauthplugin'), $dateopts);

        // Note.
        $this->_form->addElement('textarea', 'note', get_string('note', 'local_ltsauthplugin'));
        $this->_form->setType('note', PARAM_RAW);
        $this->_form->setDefault('note', '');

        $this->add_action_buttons();
    }

    /**
     * get_options_for_sub
     */
    protected function get_options_for_sub() {
        global $OUTPUT;
        $subs = submanager::get_subs_for_display();
        $options = ['' => ''];
        foreach ($subs as $sub) {
            $data = $sub->export($OUTPUT);
            $options[$data->id] = $data->name;
        }
        return $options;
    }
}
