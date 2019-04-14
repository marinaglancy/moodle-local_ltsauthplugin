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
 * Class requests_table
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin;

use local_ltsauthplugin\persistent\log;
use local_ltsauthplugin\persistent\user;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');
/**
 * Class requests_table
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class requests_table extends \table_sql {

    /**
     * requests_table constructor.
     *
     * @param string $uniqueid
     */
    public function __construct(string $uniqueid) {
        parent::__construct($uniqueid);

        $this->define_baseurl(new \moodle_url('/local/ltsauthplugin/requests.php'));
        $this->set_sql(
            'l.id, l.url, l.timecreated, l.ltsuserid, l.status, l.addinfo',
            '{' . log::TABLE . '} l LEFT JOIN {'.user::TABLE.'} u ON l.ltsuserid = u.id',
            '1=1',
            []
        );

        $this->sortable(true, 'timecreated', SORT_DESC);

        $this->define_columns([
            'timecreated', 'url', 'ltsuserid', 'status', 'addinfo'
        ]);
        // TODO use strings.
        $this->define_headers([
            'timecreated', 'url', 'ltsuserid', 'status', 'addinfo'
        ]);
    }

    /**
     * Formatter for column
     * @param \stdClass $data
     * @return string
     */
    public function col_timecreated($data) {
        return userdate($data->timecreated, get_string('strftimedatetime', 'langconfig'));
    }

    /**
     * Formatter for column
     * @param \stdClass $data
     * @return string
     */
    public function col_status($data) {
        return join(', ', log_manager::parse_statuses($data->status));
    }

    /**
     * Formatter for column
     * @param \stdClass $data
     * @return string
     */
    public function col_url($data) {
        return s($data->url);
    }

    /**
     * Formatter for column
     * @param \stdClass $data
     * @return string
     */
    public function col_addinfo($data) {
        return s($data->addinfo);
    }
}
