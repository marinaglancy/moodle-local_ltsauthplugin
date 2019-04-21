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
 * Class users_table
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin;

use local_ltsauthplugin\persistent\sub;
use local_ltsauthplugin\persistent\user;
use local_ltsauthplugin\persistent\user_site;
use local_ltsauthplugin\persistent\user_sub;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

/**
 * Class users_table
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class users_table extends \table_sql {

    /**
     * users_table constructor.
     *
     * @param string $uniqueid
     */
    public function __construct(string $uniqueid) {
        parent::__construct($uniqueid);

        $this->define_baseurl(helper::get_tab_url('users'));
        $this->set_sql(
            'u.id, u.name, u.note,' .
            helper::group_concat('st.url', '|||').' AS urls,' .
            helper::group_concat('s.plugins', ',').' AS plugins,' .
            helper::group_concat('s.name', '|||').' AS subs',
            '{'.user::TABLE.'} u '.
                'LEFT JOIN {'.user_site::TABLE.'} st ON st.ltsuserid = u.id ' .
                'LEFT JOIN {'.user_sub::TABLE.'} usb ON usb.ltsuserid = u.id AND usb.expiredate > :now ' .
                'LEFT JOIN {'.sub::TABLE.'} s ON s.id = usb.subscriptionid ',
            '1=1 '.
                'GROUP BY u.id, u.name, u.note',
            ['now' => time()]
        );
        $this->set_count_sql('SELECT COUNT(id) FROM {'.user::TABLE.'}');

        $this->sortable(true, 'timecreated', SORT_DESC);

        $this->define_columns([
            'name', 'urls', 'subs', 'plugins', 'note', 'actions'
        ]);

        $this->define_headers([
            get_string('name'),
            get_string('urls', 'local_ltsauthplugin'),
            get_string('subscriptions', 'local_ltsauthplugin'),
            get_string('plugins', 'local_ltsauthplugin'),
            get_string('note', 'local_ltsauthplugin'),
            ''
        ]);

        $this->sortable(true, 'name');
    }

    /**
     * Formatter for name
     * @param \stdClass $data
     * @return string
     */
    public function col_name($data) {
        return \html_writer::link(helper::get_tab_url('users', ['id' => $data->id]), $data->name ?: $data->id);
    }

    /**
     * Formatter for action
     * @param \stdClass $data
     * @return string
     */
    public function col_actions($data) {
        global $OUTPUT;
        return \html_writer::link(helper::get_tab_url('users', ['id' => $data->id, 'action' => 'edituser']),
            $OUTPUT->pix_icon('i/settings', get_string('edititem', 'local_ltsauthplugin'))) .
            ' ' .
            \html_writer::link(helper::get_tab_url('users', ['id' => $data->id, 'action' => 'deleteuser']),
                $OUTPUT->pix_icon('i/delete', get_string('deleteitem', 'local_ltsauthplugin')));
    }

    /**
     * Formatter for urls
     * @param \stdClass $data
     * @return string
     */
    public function col_urls($data) {
        return join('<br>', $this->regroup($data->urls, '|||'));
    }

    /**
     * Formatter for subs
     * @param \stdClass $data
     * @return string
     */
    public function col_subs($data) {
        return join('<br>', $this->regroup($data->subs, '|||'));
    }

    /**
     * Formatter for subs
     * @param \stdClass $data
     * @return string
     */
    public function col_plugins($data) {
        return join(', ', $this->regroup($data->plugins, ','));
    }

    /**
     * Formatter for subs
     * @param string $value
     * @param string $separator
     * @return array
     */
    protected function regroup($value, $separator) {
        $subs = preg_split('/'.preg_quote($separator) . '/', $value, -1, PREG_SPLIT_NO_EMPTY);
        return array_unique($subs);
    }

    /**
     * render table
     * @return string
     */
    public function render() {
        ob_start();
        $this->out(20, false);
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }
}
