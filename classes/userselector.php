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

namespace local_ltsauthplugin;

defined('MOODLE_INTERNAL') || die();

require_once __DIR__ . '/../../../user/selector/lib.php';

/*
 * This class displays either all the Moodle users allowed to use a service,
 * either all the other Moodle users.
 */

class userselector extends \user_selector_base {
    /** @var boolean Whether the conrol should allow selection of many users, or just one. */
    protected $multiselect = false;
    /** @var int The height this control should have, in rows. */
    protected $rows = 5;

    public function __construct($name, $options) {
        parent::__construct($name, $options);
    }

    /**
     * Find allowed or not allowed users of a service (depend of $this->displayallowedusers)
     *
     * @global object $DB
     * @param <type> $search
     * @return array
     */
    public function find_users($search) {
        global $DB;
        //by default wherecondition retrieves all users except the deleted, not
        //confirmed and guest
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $fields = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';
        $sql = " FROM {user} u
                 WHERE $wherecondition
                       AND u.deleted = 0 AND NOT (u.auth='webservice') ";


        list($sort, $sortparams) = users_order_by_sql('u', $search, $this->accesscontext);
        $order = ' ORDER BY ' . $sort;
        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > $this->maxusersperpage) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }
        $availableusers = $DB->get_records_sql($fields . $sql . $order, array_merge($params, $sortparams));
        if (empty($availableusers)) {
            return array();
        }

        $groupname = get_string('siteusers', 'local_ltsauthplugin');

        return array($groupname => $availableusers);
    }

    /**
     * This options are automatically used by the AJAX search
     *
     * @global object $CFG
     * @return object options pass to the constructor when AJAX search call a new selector
     */
    protected function get_options() {
        global $CFG;
        $options = parent::get_options();
        $options['file'] = '/local/ltsauthplugin/classes/userselector.php'; //need to be set, otherwise
        // the /user/selector/search.php
        //will fail to find this user_selector class
        return $options;
    }
}