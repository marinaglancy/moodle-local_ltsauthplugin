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
 * class usersubform
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\forms;

defined('MOODLE_INTERNAL') || die();

/**
 * Class usersubform
 *
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class usersubform extends baseform {

    /**
     * Custom definition
     */
    public function custom_definition() {

        $this->_form->addElement('hidden', 'userid');
        $this->_form->setType('userid', PARAM_INT);

        // Add subscriptions selector.
        $this->set_subs_field('subscriptionid', get_string('subscription', 'local_ltsauthplugin'));

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
    }
}