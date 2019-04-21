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
use local_ltsauthplugin\persistent\log_plugin;
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

        $this->define_baseurl(helper::get_tab_url('requests'));
        $this->set_sql(
            'l.id, l.url, l.timecreated, l.ltsuserid, l.status, l.addinfo, u.id as userid, u.name as username, ' .
            helper::group_concat('p.status,\':\',p.pluginname,\':\',p.pluginversion','|||') . ' AS plugins',
            '{' . log::TABLE . '} l '.
            'LEFT JOIN {'.user::TABLE.'} u ON l.ltsuserid = u.id '.
            'LEFT JOIN {'.log_plugin::TABLE.'} p ON p.logid = l.id ',
            '1=1 GROUP BY l.id, l.url, l.timecreated, l.ltsuserid, l.status, l.addinfo, u.id, u.name',
            []
        );
        $this->set_count_sql('SELECT COUNT(*) FROM {' . log::TABLE . '}');

        $this->sortable(true, 'timecreated', SORT_DESC);

        $this->define_columns([
            'recalc', 'timecreated', 'url', 'username', 'status', 'plugins', 'addinfo'
        ]);
        $this->define_headers([
            '',
            get_string('timecreated', 'local_ltsauthplugin'),
            get_string('itemurl', 'local_ltsauthplugin'),
            get_string('user'),
            get_string('status'),
            get_string('plugins', 'local_ltsauthplugin'),
            get_string('addinfo', 'local_ltsauthplugin')
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
     * Formatter for column plugins
     * @param \stdClass $data
     * @return string
     */
    public function col_plugins($data) {
        $plugins = preg_split('/'.preg_quote('|||') . '/', $data->plugins, -1, PREG_SPLIT_NO_EMPTY);
        $rv = [];
        foreach ($plugins as $plugin) {
            $parts = preg_split('/:/', $plugin, 3);
            $status = (int)array_shift($parts);
            $pluginname = array_shift($parts);
            $pluginversion = array_shift($parts);
            $rv[] = $pluginname . ' (' . $pluginversion . ') ' .
                join('', log_manager::parse_statuses($status));
        }
        return join('<br>', $rv);
    }

    /**
     * Formatter for column
     * @param \stdClass $data
     * @return string
     */
    public function col_addinfo($data) {
        return s($data->addinfo);
    }

    /**
     * Formatter for first column
     * @param \stdClass $data
     * @return string
     */
    public function col_recalc($data) {
        global $OUTPUT;
        $page = optional_param('page', null, PARAM_INT);
        $params = ['id' => $data->id, 'action' => 'rematch'];
        if ($page) {
            $params['page'] = $page;
        }
        $url = new \moodle_url($this->baseurl, $params);
        return \html_writer::link($url, $OUTPUT->pix_icon('i/reload', get_string('rematch', 'local_ltsauthplugin')));
    }

    /**
     * Formatter for username
     * @param \stdClass $data
     * @return string
     */
    public function col_username($data) {
        return $data->userid ?
            \html_writer::link(helper::get_tab_url('users', ['id' => $data->userid]), $data->username) :
            '';
    }

    /**
     * render table
     * @return string
     */
    public function render() {
        ob_start();
        $this->out(50, false);
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }
}
