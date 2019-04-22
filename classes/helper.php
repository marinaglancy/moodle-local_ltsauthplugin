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
 * Class helper
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin;

use local_ltsauthplugin\output\renderer;
use local_ltsauthplugin\persistent\log;
use local_ltsauthplugin\persistent\user;
use local_ltsauthplugin\persistent\user_site;
use local_ltsauthplugin\subscription\pluginmanager;
use local_ltsauthplugin\subscription\submanager;
use local_ltsauthplugin\subscription\usersubmanager;
use local_ltsauthplugin\user\usermanager;
use local_ltsauthplugin\user\usersitemanager;

defined('MOODLE_INTERNAL') || die();

/**
 * Class helper
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * list of tabs
     * @return array
     */
    public static function get_tabs(): array {
        return [
            'users' => get_string('authplugin_user', 'local_ltsauthplugin'),
            'subs' => get_string('authplugin_subscription', 'local_ltsauthplugin'),
            'requests' => get_string('requests', 'local_ltsauthplugin'),
            'stats' => get_string('stats', 'local_ltsauthplugin'),
        ];
    }

    /**
     * current tab
     * @return string
     */
    public static function get_current_tab(): string {
        $currenttab = optional_param('tab', null, PARAM_ALPHANUMEXT);
        $action = optional_param('action', null, PARAM_ALPHANUMEXT);
        if (!array_key_exists($currenttab, self::get_tabs())) {
            if (in_array($action, ['editplugin', 'deleteplugin', 'editsub', 'deletesub'])) {
                $currenttab = 'subs';
            } else {
                $currenttab = 'users';
            }
        }
        return $currenttab;
    }

    /**
     * tab url
     * @param string $tabkey
     * @param array $params
     * @return \moodle_url
     */
    public static function get_tab_url(string $tabkey, array $params = []): \moodle_url {
        return new \moodle_url('/local/ltsauthplugin/index.php', ['tab' => $tabkey] + $params);
    }

    /**
     * user edit form
     * @param int $userid
     * @return form\user
     */
    public static function get_user_edit_form($userid) {
        $item = new \local_ltsauthplugin\persistent\user($userid);
        $mform = new \local_ltsauthplugin\form\user(null, ['persistent' => $item]);
        $redirecturl = self::get_tab_url('users');
        if ($mform->is_cancelled()) {
            redirect($redirecturl);
        } else if ($data = $mform->get_data()) {
            usermanager::save($item, $data);
            redirect($redirecturl);
        }
        return $mform;
    }

    /**
     * get_user_edit_site_form
     * @param int $id
     * @param int $ltsuserid
     * @return form\user_site
     */
    public static function get_user_edit_site_form(?int $id, int $ltsuserid) {
        $item = new \local_ltsauthplugin\persistent\user_site($id);
        if (!$id) {
            $item->set('ltsuserid', $ltsuserid);
        }
        $mform = new \local_ltsauthplugin\form\user_site(null, ['persistent' => $item]);
        $redirecturl = self::get_tab_url('users', ['id' => $item->get('ltsuserid')]);
        if ($mform->is_cancelled()) {
            redirect($redirecturl);
        } else if ($data = $mform->get_data()) {
            require_sesskey();
            usersitemanager::save($item, $data);
            redirect($redirecturl);
        }
        return $mform;
    }

    /**
     * get_user_edit_sub_form
     * @param int $id
     * @param int $ltsuserid
     * @return form\user_sub
     */
    public static function get_user_edit_sub_form(?int $id, int $ltsuserid) {
        $item = new \local_ltsauthplugin\persistent\user_sub($id);
        if (!$id) {
            $item->set('ltsuserid', $ltsuserid);
        }
        $mform = new \local_ltsauthplugin\form\user_sub(null, ['persistent' => $item]);
        $redirecturl = self::get_tab_url('users', ['id' => $item->get('ltsuserid')]);
        if ($mform->is_cancelled()) {
            redirect($redirecturl);
        } else if ($data = $mform->get_data()) {
            require_sesskey();
            usersubmanager::save($item, $data);
            redirect($redirecturl);
        }
        return $mform;
    }

    /**
     * get_edit_plugin_form
     * @param int $id
     * @return form\plugin
     */
    public static function get_edit_plugin_form(?int $id) {
        $item = new \local_ltsauthplugin\persistent\plugin($id);
        $mform = new \local_ltsauthplugin\form\plugin(null, ['persistent' => $item]);
        $redirecturl = self::get_tab_url('subs');
        if ($mform->is_cancelled()) {
            redirect($redirecturl);
        } else if ($data = $mform->get_data()) {
            pluginmanager::save($item, $data);
            redirect($redirecturl);
        }
        return $mform;
    }

    /**
     * get_edit_sub_form
     * @param int $id
     * @return form\sub
     */
    public static function get_edit_sub_form(?int $id) {
        $item = new \local_ltsauthplugin\persistent\sub($id);
        $mform = new \local_ltsauthplugin\form\sub(null, ['persistent' => $item]);
        $redirecturl = self::get_tab_url('subs');
        if ($mform->is_cancelled()) {
            redirect($redirecturl);
        } else if ($data = $mform->get_data()) {
            submanager::save($item, $data);
            redirect($redirecturl);
        }
        return $mform;
    }

    /**
     * Display current tab
     * @param renderer $renderer
     * @return string
     */
    public static function display_tab(renderer $renderer) {
        $currenttab = self::get_current_tab();
        if ($currenttab === 'users') {
            return static::display_tab_users($renderer);
        } else if ($currenttab === 'subs') {
            return static::display_tab_subs($renderer);
        } else if ($currenttab === 'requests') {
            return static::display_tab_requests($renderer);
        } else if ($currenttab === 'stats') {
            return static::display_tab_stats($renderer);
        }
        return '';
    }

    /**
     * Delete site for a user.
     * @param int $id
     * @return string
     */
    protected static function delete_user_site($id) {
        global $OUTPUT;
        $action = 'deleteusersite';
        $confirm = optional_param('sesskey', null, PARAM_BOOL);
        $item = new user_site($id);
        if ($confirm && confirm_sesskey()) {
            usersitemanager::delete($item);
            redirect(self::get_tab_url('users', ['id' => $item->get('ltsuserid')]));
        } else {
            $name = $item->get('url');
            return $OUTPUT->confirm(get_string("confirmitemdelete", "local_ltsauthplugin", $name),
                self::get_tab_url('users', ['action' => $action, 'id' => $id]),
                self::get_tab_url('users', ['id' => $item->get('ltsuserid')]));
        }
    }

    /**
     * Delete site for a user.
     * @param int $id
     * @return string
     */
    protected static function delete_user_sub($id) {
        global $OUTPUT;
        $action = 'deleteusersub';
        $confirm = optional_param('sesskey', null, PARAM_BOOL);
        $item = new \local_ltsauthplugin\persistent\user_sub($id);
        if ($confirm && confirm_sesskey()) {
            usersubmanager::delete($item);
            redirect(self::get_tab_url('users', ['id' => $item->get('ltsuserid')]));
        } else {
            $name = $item->get('id');
            return $OUTPUT->confirm(get_string("confirmitemdelete", "local_ltsauthplugin", $name),
                self::get_tab_url('users', ['action' => $action, 'id' => $id]),
                self::get_tab_url('users', ['id' => $item->get('ltsuserid')]));
        }
    }

    /**
     * Delete a user.
     * @param int $id
     * @return string
     */
    protected static function delete_user($id) {
        global $OUTPUT;
        $action = 'deleteuser';
        $confirm = optional_param('sesskey', null, PARAM_BOOL);
        $item = new \local_ltsauthplugin\persistent\user($id);
        if ($confirm && confirm_sesskey()) {
            usermanager::delete($item);
            redirect(self::get_tab_url('users'));
        } else {
            $name = $item->get('name');
            return $OUTPUT->confirm(get_string("confirmitemdelete", "local_ltsauthplugin", $name),
                self::get_tab_url('users', ['action' => $action, 'id' => $id]),
                self::get_tab_url('users'));
        }
    }

    /**
     * Display users tab
     * @param renderer $renderer
     * @return string
     */
    protected static function display_tab_users(renderer $renderer) {
        $action = optional_param('action', null, PARAM_ALPHANUMEXT);
        $ltsuserid = optional_param('ltsuserid', null, PARAM_INT);
        $id = optional_param('id', null, PARAM_INT);
        $rv = '';
        if ($action === 'edituser') {
            // Edit user.
            $mform = self::get_user_edit_form($id);
            $rv .= $mform->render();
        } else if ($action === 'deleteuser' && $id) {
            // Delete site for a user.
            return self::delete_user($id);
        } else if ($action === 'editusersite') {
            // Add or edit a site for a user.
            $mform = self::get_user_edit_site_form($id, $ltsuserid);
            $rv .= $mform->render();
        } else if ($action === 'deleteusersite' && $id) {
            // Delete site for a user.
            return self::delete_user_site($id);
        } else if ($action === 'editusersub') {
            // Add or edit a subscription for a user.
            $mform = self::get_user_edit_sub_form($id, $ltsuserid);
            $rv .= $mform->render();
        } else if ($action === 'deleteusersub' && $id) {
            // Delete a scubscription for a user.
            return self::delete_user_sub($id);
        } else if ($id) {
            // View a user.
            // In this case id - userid.
            $user = new user($id);
            $authpluginuser = $user->to_record();
            $siteitems = usersitemanager::get_user_sites_for_display($user);
            $subsitems = usersubmanager::get_user_subs_for_display($user);

            $rv .= $renderer->show_user_summary($authpluginuser);
            $rv .= $renderer->add_siteitem_link($authpluginuser);
            $rv .= $renderer->show_user_sites_list($siteitems);
            $rv .= $renderer->add_subsitem_link($authpluginuser);
            $rv .= $renderer->show_user_subs_list($subsitems);
        } else {
            // Display list of users.
            $rv .= \html_writer::div(\html_writer::link(self::get_tab_url('users', ['action' => 'edituser']),
                get_string('addnewuser', 'local_ltsauthplugin')));
            $table = new \local_ltsauthplugin\users_table('local_ltsauthplugin_userslist');
            $rv .= $table->render();
        }
        return $rv;
    }

    /**
     * Delete plugin
     * @param int $id
     * @return string
     */
    protected static function delete_plugin($id) {
        global $OUTPUT;
        $action = 'deleteplugin';
        $confirm = optional_param('sesskey', null, PARAM_BOOL);
        $item = new \local_ltsauthplugin\persistent\plugin($id);
        if ($confirm && confirm_sesskey()) {
            pluginmanager::delete($item);
            redirect(self::get_tab_url('subs'));
        } else {
            $name = $item->get('name');
            return $OUTPUT->confirm(get_string("confirmitemdelete", "local_ltsauthplugin", $name),
                self::get_tab_url('subs', ['action' => $action, 'id' => $id]),
                self::get_tab_url('subs'));
        }
    }

    /**
     * Delete sub
     * @param int $id
     * @return string
     */
    protected static function delete_sub($id) {
        global $OUTPUT;
        $action = 'deletesub';
        $confirm = optional_param('sesskey', null, PARAM_BOOL);
        $item = new \local_ltsauthplugin\persistent\sub($id);
        if ($confirm && confirm_sesskey()) {
            submanager::delete($item);
            redirect(self::get_tab_url('subs'));
        } else {
            $name = $item->get('name');
            return $OUTPUT->confirm(get_string("confirmitemdelete", "local_ltsauthplugin", $name),
                self::get_tab_url('subs', ['action' => $action, 'id' => $id]),
                self::get_tab_url('subs'));
        }
    }

    /**
     * Display users subs
     * @param renderer $renderer
     * @return string
     */
    protected static function display_tab_subs($renderer) {
        $action = optional_param('action', null, PARAM_ALPHANUMEXT);
        $id = optional_param('id', null, PARAM_INT);
        $rv = '';
        if ($action === 'editsub') {
            $rv .= self::get_edit_sub_form($id)->render();
        } else if ($action === 'deletesub' && $id) {
            $rv .= self::delete_sub($id);
        } else if ($action === 'editplugin') {
            $rv .= self::get_edit_plugin_form($id)->render();
        } else if ($action === 'deleteplugin' && $id) {
            $rv .= self::delete_plugin($id);
        } else {
            $subs = submanager::get_subs_for_display();
            $plugins = pluginmanager::get_plugins_for_display();
            $rv .= $renderer->add_sub_link();
            $rv .= $renderer->show_subs_list($subs);
            $rv .= $renderer->add_plugin_link();
            $rv .= $renderer->show_plugins_list($plugins);
        }
        return $rv;
    }

    /**
     * Display users requests
     * @param renderer $renderer
     * @return string
     */
    protected static function display_tab_requests($renderer) {
        $action = optional_param('action', null, PARAM_ALPHANUMEXT);
        $id = optional_param('id', null, PARAM_INT);
        $page = optional_param('page', null, PARAM_INT);
        $rv = '';
        if ($action === 'rematch' && $id) {
            log_manager::update_log_status(new log($id));
            redirect(self::get_tab_url('requests', ['page' => $page]));
        }
        $table = new \local_ltsauthplugin\requests_table('local_ltsauthplugin_requestslog');
        $rv .= $table->render();
        return $rv;
    }

    /**
     * Get the SQL for group concatenation
     *
     * @param string $field
     * @param string $separator
     * @return string
     * @throws \coding_exception
     */
    public static function group_concat(string $field, ?string $separator = null) : string {
        global $DB;
        $dbfamily = $DB->get_dbfamily();
        $separator = ($separator === null) ? get_string('listsep', 'langconfig') . ' ' : $separator;
        switch ($dbfamily) {
            case 'mssql':
                return " dbo.GROUP_CONCAT_D($field) ";
                break;
            case 'postgres':
                $cleanfield = str_replace(',', ' || ', $field);
                $orderby = implode(',', array_filter(explode(",", $field)));
                return " STRING_AGG(CAST($cleanfield AS VARCHAR), '{$separator}' ORDER BY {$orderby})";
                break;
            case 'mysql':
            case 'oracle':
                return "GROUP_CONCAT($field)";
                break;
        }
    }

    /**
     * Display the statistics
     * @param renderer $renderer
     * @param bool $showmoreinfo
     * @return string
     */
    public static function display_tab_stats(renderer $renderer, bool $showmoreinfo = true) {
        // TODO if necessary add strings here.
        $rv = '';
        // Expired in the last 24 hours.
        if (($expiredsubs = usersubmanager::get_expired(DAYSECS)) || $showmoreinfo) {
            $rv .= \html_writer::tag('h3', 'Subscriptions expired in the last 24 hours');
            $rv .= $renderer->show_user_subs_list($expiredsubs, true, $showmoreinfo);
        }
        // Expired in the last week.
        if (($expiredsubs = usersubmanager::get_expired(7 * DAYSECS)) || $showmoreinfo) {
            $rv .= \html_writer::tag('h3', 'Subscriptions expired in the last 7 days');
            $rv .= $renderer->show_user_subs_list($expiredsubs, true, $showmoreinfo);
        }
        // No call home.
        if (($usersites = usersitemanager::get_user_sites_without_logs(3 * DAYSECS)) || $showmoreinfo) {
            $rv .= \html_writer::tag('h3', 'Sites without any requests in the last 3 days');
            $rv .= $renderer->show_user_sites_list($usersites, true, $showmoreinfo);
        }
        // Not ok requests.
        if (($countrequests = log_manager::count_not_ok_records(DAYSECS)) || $showmoreinfo) {
            $requests = log_manager::get_not_ok_records(DAYSECS);
            $rv .= \html_writer::tag('h3', 'Requests in the last 24 hours that have errors in the statuses');
            $rv .= $renderer->show_requests($requests);
            $url = self::get_tab_url('requests', ['statusmask' => log_manager::get_not_ok_statuses_mask()]);
            if ($countrequests > 10) {
                $rv .= \html_writer::div(\html_writer::link($url, "Found {$countrequests}, showing only first 10, see more here"));
            } else if ($countrequests) {
                $rv .= \html_writer::div(\html_writer::link($url, "More details"));
            }
        }
        return $rv;
    }
}
