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
 * Class log_manager
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin;

use core\persistent;
use local_ltsauthplugin\persistent\log;
use local_ltsauthplugin\persistent\log_plugin;
use local_ltsauthplugin\persistent\plugin;
use local_ltsauthplugin\persistent\sub;
use local_ltsauthplugin\persistent\user;
use local_ltsauthplugin\persistent\user_site;
use local_ltsauthplugin\persistent\user_sub;

defined('MOODLE_INTERNAL') || die();

/**
 * Class log_manager
 *
 * @package     local_ltsauthplugin
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class log_manager {

    /** @var int */
    const STATUS_URL_UNKNOWN = 1;
    /** @var int */
    const STATUS_SUB_EXPIRED = 2;
    /** @var int */
    const STATUS_SUB_ACTIVE = 4;
    /** @var int */
    const STATUS_SUB_MISSING = 8;
    /** @var int */
    const STATUS_PLUGIN_UNKNOWN = 16;

    /**
     * Add to log
     *
     * @param string $url
     * @param string $pluginsstr
     * @param array $addinfo
     */
    public static function add_to_log(string $url, string $pluginsstr, ?array $addinfo = null) {
        $knownurls = self::get_known_urls_map();

        $log = new log();
        $url = strtolower($url);
        $log->set('url', $url);
        if (array_key_exists($url, $knownurls)) {
            $ltsuserid = $knownurls[$url];
            $log->set('ltsuserid', $ltsuserid);
        }
        if ($addinfo !== null) {
            $log->set('addinfo', json_encode($addinfo));
        }
        $log->save();

        $logplugins = [];
        $plugins = preg_split('/,/', $pluginsstr);
        foreach ($plugins as $pluginname) {
            $parts = preg_split('/:/', strtolower(trim($pluginname)), 2);
            $pluginname = $parts[0];
            if (!strlen($pluginname)) {
                continue;
            }
            $logplugin = new log_plugin();
            $logplugin->set('logid', $log->get('id'));
            $logplugin->set('pluginname', $pluginname);
            $logplugin->set('pluginversion', isset($parts[1]) ? $parts[1] : '');
            $logplugin->save();
            $logplugins[$logplugin->get('id')] = $logplugin;
        }

        self::update_log_status($log, $logplugins);
    }

    /**
     * Update status on the log entry and the log plugins
     *
     * @param log $log
     * @param array|null $plugins
     */
    public static function update_log_status(log $log, ?array $plugins = null) {
        $knownurls = self::get_known_urls_map();
        $knownplugins = self::get_known_plugins_map();

        $url = $log->get('url');
        if (!array_key_exists($url, $knownurls)) {
            // URL not recognised.
            if (self::update_if_changed($log, ['status' => self::STATUS_URL_UNKNOWN])) {
                $plugins = $plugins ?: log_plugin::get_records(['logid' => $log->get('id')]);
                foreach ($plugins as $plugin) {
                    if (array_key_exists($plugin->get('pluginname'), $knownplugins)) {
                        $pluginid = $knownplugins[$plugin->get('pluginname')];
                        self::update_if_changed($plugin, ['pluginid' => $pluginid, 'status' => self::STATUS_URL_UNKNOWN]);
                    } else {
                        self::update_if_changed($plugin, ['pluginid' => null, 'status' => self::STATUS_PLUGIN_UNKNOWN]);
                    }
                }
            }
            return;
        }

        // We found an "owner" of the site. Now let's check subscription.
        $ltsuserid = $knownurls[$url];
        [$validplugins, $expiredplugins] = self::get_subscription_plugins($ltsuserid);

        $logstatus = 0;
        $plugins = $plugins ?: log_plugin::get_records(['logid' => $log->get('id')]);
        foreach ($plugins as $logplugin) {
            $pluginid = null;
            if (array_key_exists($logplugin->get('pluginname'), $knownplugins)) {
                $pluginid = $knownplugins[$logplugin->get('pluginname')];
            }
            if (in_array($logplugin->get('pluginname'), $validplugins)) {
                self::update_if_changed($logplugin, ['pluginid' => $pluginid, 'status' => self::STATUS_SUB_ACTIVE]);
                $logstatus = $logstatus | self::STATUS_SUB_ACTIVE;
            } else if (in_array($logplugin->get('pluginname'), $expiredplugins)) {
                self::update_if_changed($logplugin, ['pluginid' => $pluginid, 'status' => self::STATUS_SUB_EXPIRED]);
                $logstatus = $logstatus | self::STATUS_SUB_EXPIRED;
            } else if ($pluginid) {
                self::update_if_changed($logplugin, ['pluginid' => $pluginid, 'status' => self::STATUS_SUB_MISSING]);
                $logstatus = $logstatus | self::STATUS_SUB_MISSING;
            } else {
                self::update_if_changed($logplugin, ['pluginid' => $pluginid, 'status' => self::STATUS_PLUGIN_UNKNOWN]);
                $logstatus = $logstatus | self::STATUS_PLUGIN_UNKNOWN;
            }
        }

        self::update_if_changed($log, ['ltsuserid' => $ltsuserid, 'status' => $logstatus]);
    }

    /**
     * Updates properties of the persistent if needed
     *
     * @param persistent $element
     * @param array $data
     * @return bool if there were changes
     */
    private static function update_if_changed(persistent $element, array $data) : bool {
        $changed = false;
        foreach ($data as $key => $value) {
            if ($element->get($key) !== $value) {
                $element->set($key, $value);
                $changed = true;
            }
        }
        if ($changed) {
            $element->save();
        }
        return $changed;
    }

    /**
     * Get known plugins array name=>id
     * @return array
     */
    private static function get_known_plugins_map() : array {
        $registeredplugins = plugin::get_records([]);
        $map = [];
        foreach ($registeredplugins as $record) {
            $map[$record->get('name')] = $record->get('id');
        }
        return $map;
    }

    /**
     * Get registered sites array url=>ltsuserid
     * @return array
     */
    private static function get_known_urls_map() : array {
        $registeredsites = user_site::get_records([]);
        $map = [];
        foreach ($registeredsites as $record) {
            $map[$record->get('url')] = $record->get('ltsuserid');
        }
        return $map;
    }

    /**
     * Get plugins in user subscription
     *
     * @param int $ltsuserid
     * @return array
     */
    private static function get_subscription_plugins(int $ltsuserid) : array {
        global $DB;
        /** @var user_sub[] $usersubs */
        $usersubs = user_sub::get_records(['ltsuserid' => $ltsuserid]);
        if (!$usersubs) {
            return [];
        }

        $ids = array_map(function(persistent $p) {
            return $p->get('subscriptionid');
        }, $usersubs);

        list($sql, $params) = $DB->get_in_or_equal($ids);
        /** @var sub[] $subs */
        $subs = sub::get_records_select('id ' . $sql, $params);

        $validplugins = $expiredplugins = [];
        foreach ($usersubs as $usersub) {
            if (!array_key_exists($usersub->get('subscriptionid'), $subs)) {
                continue;
            }
            $plugins = $subs[$usersub->get('subscriptionid')]->get_plugins_list();
            if (!$usersub->is_expired()) {
                $validplugins = array_merge($validplugins, $plugins);
            } else {
                $expiredplugins = array_merge($expiredplugins, $plugins);
            }
        }
        $validplugins = array_unique($validplugins);
        // Same plugin can be in different subscriptions and only one of them could have expired. It is still valid.
        $expiredplugins = array_diff(array_unique($expiredplugins), $validplugins);

        return [$validplugins, $expiredplugins];
    }

    /**
     * Get statuses as array
     *
     * @param int $status
     * @return array
     */
    public static function parse_statuses(int $status) {
        $allstatuses = [
            self::STATUS_URL_UNKNOWN => get_string('statusurlunknown', 'local_ltsauthplugin'),
            self::STATUS_PLUGIN_UNKNOWN => get_string('statuspluginunknown', 'local_ltsauthplugin'),
            self::STATUS_SUB_ACTIVE => get_string('statusok', 'local_ltsauthplugin'),
            self::STATUS_SUB_MISSING => get_string('statussubmissing', 'local_ltsauthplugin'),
            self::STATUS_SUB_EXPIRED => get_string('statussubexpired', 'local_ltsauthplugin')
        ];
        $rv = [];
        foreach ($allstatuses as $key => $value) {
            if ($status & $key) {
                $rv[] = \html_writer::span($value, self::get_status_class($key));
            }
        }
        return $rv;
    }

    /**
     * CSS classes for statuses
     * @param int $status
     * @return string
     */
    public static function get_status_class(int $status): string {
        if ($status == self::STATUS_SUB_EXPIRED) {
            return 'badge-warning tag';
        } else if ($status == self::STATUS_URL_UNKNOWN || $status == self::STATUS_PLUGIN_UNKNOWN) {
            return 'badge-secondary tag';
        } else if ($status == self::STATUS_SUB_MISSING) {
            return 'badge-danger tag';
        } else if ($status == self::STATUS_SUB_ACTIVE) {
            return '';
        }
    }

    /**
     * Mask for all statuses except OK.
     * @return int
     */
    public static function get_not_ok_statuses_mask(): int {
        return self::STATUS_SUB_MISSING | self::STATUS_PLUGIN_UNKNOWN | self::STATUS_URL_UNKNOWN | self::STATUS_SUB_EXPIRED;
    }

    /**
     * Counts number of records with not OK status
     * @param int $period
     * @return int
     */
    public static function count_not_ok_records(int $period) {
        global $DB;
        $statusmask = self::get_not_ok_statuses_mask();
        $where = 'timecreated > :start AND (' . $DB->sql_bitand('l.status', ':statusmask') . ' <> 0)';
        $params = ['statusmask' => $statusmask, 'start' => time() - $period];
        return $DB->count_records_sql('SELECT COUNT(*) FROM {' . log::TABLE . '} l WHERE ' . $where, $params);
    }

    /**
     * Get last 10 records with not OK status
     *
     * @param int $period
     * @return array
     */
    public static function get_not_ok_records(int $period) {
        global $DB;
        $statusmask = self::get_not_ok_statuses_mask();
        $where = 'l.timecreated > :start AND (' . $DB->sql_bitand('l.status', ':statusmask') . ' <> 0)';
        $params = ['statusmask' => $statusmask, 'start' => time() - $period];
        return $DB->get_records_sql('SELECT l.id, l.timecreated, l.url, l.ltsuserid, u.name as username, l.status
            FROM {' . log::TABLE . '} l
            LEFT JOIN {' . user::TABLE . '} u ON u.id = l.ltsuserid
            WHERE ' . $where . '
            ORDER BY timecreated DESC', $params, 0, 10);
    }
}
