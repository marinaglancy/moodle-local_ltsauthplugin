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
 * class renderer
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ltsauthplugin\output;

defined('MOODLE_INTERNAL') || die();

/**
 * class renderer
 * @package   local_ltsauthplugin
 * @copyright 2016 Poodll Co. Ltd (https://poodll.com)
 * @author    Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * User selection form.
     * @param \user_selector_base $userselector
     * @return string
     */
    public function user_selection_form(\user_selector_base $userselector) {
        $output = '';
        $formattributes = array();
        $formattributes['id'] = 'userselectionform';
        $formattributes['action'] = $this->page->url;
        $formattributes['method'] = 'post';
        \html_writer::start_div('userselector');
        $output .= \html_writer::start_tag('form', $formattributes);
        $output .= \html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
        $output .= $userselector->display(true);
        $output .= \html_writer::empty_tag('input', array(
                'type' => 'submit',
                'name' => 'Choose',
                'value' => get_string('chooseuser', 'local_ltsauthplugin'),
                'class' => 'actionbutton')
        );

        $output .= \html_writer::end_tag('form');
        \html_writer::start_div('userselector');
        return $output;
    }

    /**
     * show user summary
     *
     * @param \stdClass $selecteduser
     * @param \stdClass $authpluginuser
     * @return string
     */
    public function show_user_summary($selecteduser, $authpluginuser) {
        global $DB;

        $output = $this->output->heading(get_string("userheader", "local_ltsauthplugin", $selecteduser), 3);
        $theuser = $DB->get_record('user', array('id' => $selecteduser->id));
        $selecteduser->username = $theuser->username;

        $table = new \html_table();
        $table->id = 'local_ltsauthplugin_usersummary';
        $table->head = array(
            'ID',
            get_string('username'),
            get_string('note', 'local_ltsauthplugin'),
            get_string('actions', 'local_ltsauthplugin')
        );
        $table->headspan = array(1, 1, 1, 1);
        $table->colclasses = array(
            'id', 'username', 'note', 'edit'
        );

        $row = new \html_table_row();
        $row->cells = array();

        $idcell = new \html_table_cell($authpluginuser->id);
        $row->cells[] = $idcell;

        $usernamecell = new \html_table_cell($selecteduser->username);
        $row->cells[] = $usernamecell;

        $notecell = new \html_table_cell($authpluginuser->note);
        $row->cells[] = $notecell;

        $actionurl = '/local/ltsauthplugin/usersite/manageusers.php';
        $editurl = new \moodle_url($actionurl, array('userid' => $authpluginuser->userid));
        $editlink = \html_writer::link($editurl, get_string('edititem', 'local_ltsauthplugin'));
        $editcell = new \html_table_cell($editlink);
        $row->cells[] = $editcell;

        $table->data[] = $row;

        return $output . \html_writer::table($table);
    }

    /**
     * add siteitem link
     *
     * @param \stdClass $selecteduser
     * @param \stdClass $authpluginuser
     * @return string
     */
    public function add_siteitem_link($selecteduser, $authpluginuser) {
        global $CFG;

        $output = $this->output->heading(get_string("showitemsfor", "local_ltsauthplugin", $selecteduser), 4);
        $links = array();

        $additemurl = new \moodle_url('/local/ltsauthplugin/usersite/manageusersites.php',
            array('ltsuserid' => $authpluginuser->id));
        $links[] = \html_writer::link($additemurl, get_string('addnewitem', 'local_ltsauthplugin'));

        return $this->output->box($output . '<p>' . implode('</p><p>', $links) . '</p>', 'generalbox firstpageoptions');
    }

    /**
     * Return the html table of homeworks for a group  / course
     *
     * @param user_site_exporter[] $sites
     * @return string html of table
     */
    public function show_siteitems_list($sites) {
        global $DB;

        if (!$sites) {
            return $this->output->heading(get_string('noitems', 'local_ltsauthplugin'), 3, 'main');
        }

        $table = new \html_table();
        $table->id = 'local_ltsauthplugin_itempanel';
        $table->head = array(
            get_string('itemid', 'local_ltsauthplugin'),
            get_string('itemurl', 'local_ltsauthplugin'),
            get_string('note', 'local_ltsauthplugin'),
            get_string('actions', 'local_ltsauthplugin')
        );
        $table->headspan = array(1, 1, 1, 2);
        $table->colclasses = array(
            'id', 'url', 'note', 'edit', 'delete'
        );

        // Loop through the items and add to table.
        foreach ($sites as $site) {
            $item = $site->export($this->output);
            $row = new \html_table_row();
            $row->cells = array();

            $itemidcell = new \html_table_cell($item->id);
            $row->cells[] = $itemidcell;

            $urlcell = new \html_table_cell($item->url);
            $row->cells[] = $urlcell;

            $notecell = new \html_table_cell($item->note);
            $row->cells[] = $notecell;

            $itemactionurl = '/local/ltsauthplugin/usersite/manageusersites.php';
            $itemediturl = new \moodle_url($itemactionurl, array('ltsuserid' => $item->ltsuserid, 'id' => $item->id));
            $itemeditlink = \html_writer::link($itemediturl, get_string('edititem', 'local_ltsauthplugin'));
            $itemeditcell = new \html_table_cell($itemeditlink);
            $row->cells[] = $itemeditcell;

            $itemdeleteurl = new \moodle_url($itemactionurl, array('ltsuserid' => $item->ltsuserid,
                'id' => $item->id, 'action' => 'confirmdelete'));
            $itemdeletelink = \html_writer::link($itemdeleteurl, get_string('deleteitem', 'local_ltsauthplugin'));
            $itemdeletecell = new \html_table_cell($itemdeletelink);
            $row->cells[] = $itemdeletecell;

            $table->data[] = $row;
        }

        return \html_writer::table($table);
    }

    /**
     * add subs item link
     *
     * @param \stdClass $selecteduser
     * @param \stdClass $authpluginuser
     * @return string
     */
    public function add_subsitem_link($selecteduser, $authpluginuser) {
        global $CFG;

        $output = $this->output->heading(get_string("showsubsfor", "local_ltsauthplugin", $selecteduser), 4);
        $links = array();

        $additemurl = new \moodle_url('/local/ltsauthplugin/subscriptions/manageusersubs.php',
            array('ltsuserid' => $authpluginuser->id));
        $links[] = \html_writer::link($additemurl, get_string('addnewsub', 'local_ltsauthplugin'));

        return $this->output->box($output . '<p>' . implode('</p><p>', $links) . '</p>', 'generalbox firstpageoptions');
    }

    /**
     * Return the html table of subscriptions for a user
     *
     * @param user_sub_exporter[] $usersubs
     * @return string html of table
     */
    public function show_subsitems_list($usersubs) {
        global $DB;

        if (!$usersubs) {
            return $this->output->heading(get_string('nosubs', 'local_ltsauthplugin'), 3, 'main');
        }

        $table = new \html_table();
        $table->id = 'local_ltsauthplugin_subsitempanel';
        $table->head = array(
            'ID',
            get_string('subscriptionname', 'local_ltsauthplugin'),
            get_string('note', 'local_ltsauthplugin'),
            get_string('expiredate', 'local_ltsauthplugin'),
            get_string('actions', 'local_ltsauthplugin')
        );
        $table->headspan = array(1, 1, 1, 1, 2);
        $table->colclasses = array(
            'id', 'name', 'note', 'expiredate', 'edit', 'delete'
        );

        $items = [];
        foreach ($usersubs as $usersub) {
            $items[] = $usersub->export($this->output);
        }

        // Sort by start date.
        \core_collator::asort_objects_by_property($items, 'name');

        // Loop through the items and add to table.
        foreach ($items as $item) {
            $row = new \html_table_row();
            $row->cells = array();

            $itemidcell = new \html_table_cell($item->id);
            $row->cells[] = $itemidcell;

            $namecell = new \html_table_cell($item->name);
            $row->cells[] = $namecell;

            $notecell = new \html_table_cell($item->note);
            $row->cells[] = $notecell;

            $itemexpiredatecell = new \html_table_cell(($item->expiredate ? date("d/m/Y", $item->expiredate) : '--'));
            $row->cells[] = $itemexpiredatecell;

            $itemactionurl = '/local/ltsauthplugin/subscriptions/manageusersubs.php';
            $itemediturl = new \moodle_url($itemactionurl, array('ltsuserid' => $item->ltsuserid, 'id' => $item->id));
            $itemeditlink = \html_writer::link($itemediturl, get_string('editsub', 'local_ltsauthplugin'));
            $itemeditcell = new \html_table_cell($itemeditlink);
            $row->cells[] = $itemeditcell;

            $itemdeleteurl = new \moodle_url($itemactionurl, array('ltsuserid' => $item->ltsuserid,
                'id' => $item->id, 'action' => 'confirmdelete'));
            $itemdeletelink = \html_writer::link($itemdeleteurl, get_string('deletesub', 'local_ltsauthplugin'));
            $itemdeletecell = new \html_table_cell($itemdeletelink);
            $row->cells[] = $itemdeletecell;

            $table->data[] = $row;
        }

        return \html_writer::table($table);
    }

    /**
     * Show the add subscription button
     */
    public function add_sub_link() {
        global $CFG;

        $output = $this->output->heading(get_string("showsubs", "local_ltsauthplugin"), 4);
        $links = array();

        $additemurl = new \moodle_url('/local/ltsauthplugin/subscriptions/managesubs.php', array());
        $links[] = \html_writer::link($additemurl, get_string('addnewsub', 'local_ltsauthplugin'));

        return $this->output->box($output . '<p>' . implode('</p><p>', $links) . '</p>', 'generalbox firstpageoptions');
    }

    /**
     * Return the html table of subscriptions
     *
     * @param sub_exporter[] $subs
     * @return string html of table
     */
    public function show_subs_list($subs) {
        global $DB;

        if (!$subs) {
            return $this->output->heading(get_string('nosubs', 'local_ltsauthplugin'), 3, 'main');
        }

        $table = new \html_table();
        $table->id = 'local_ltsauthplugin_subsitempanel';
        $table->head = array(
            'ID',
            get_string('subscriptionname', 'local_ltsauthplugin'),
            get_string('plugins', 'local_ltsauthplugin'),
            get_string('note', 'local_ltsauthplugin'),
            get_string('actions', 'local_ltsauthplugin')
        );
        $table->headspan = array(1, 1, 1, 1, 2);
        $table->colclasses = array(
            'id', 'name', 'plugins', 'note', 'edit', 'delete'
        );

        // Loop through the items and add to table.
        foreach ($subs as $sub) {
            $item = $sub->export($this->output);

            $row = new \html_table_row();
            $row->cells = array();

            $itemidcell = new \html_table_cell($item->id);
            $row->cells[] = $itemidcell;

            $namecell = new \html_table_cell($item->name);
            $row->cells[] = $namecell;

            $pluginscell = new \html_table_cell($item->plugins);
            $row->cells[] = $pluginscell;

            $notecell = new \html_table_cell($item->note);
            $row->cells[] = $notecell;

            $itemactionurl = '/local/ltsauthplugin/subscriptions/managesubs.php';
            $itemediturl = new \moodle_url($itemactionurl, array('id' => $item->id));
            $itemeditlink = \html_writer::link($itemediturl, get_string('editsub', 'local_ltsauthplugin'));
            $itemeditcell = new \html_table_cell($itemeditlink);
            $row->cells[] = $itemeditcell;

            $itemdeleteurl = new \moodle_url($itemactionurl, array('id' => $item->id, 'action' => 'confirmdelete'));
            $itemdeletelink = \html_writer::link($itemdeleteurl, get_string('deletesub', 'local_ltsauthplugin'));
            $itemdeletecell = new \html_table_cell($itemdeletelink);
            $row->cells[] = $itemdeletecell;

            $table->data[] = $row;
        }

        return \html_writer::table($table);
    }

    /**
     * Show the add subscription button
     */
    public function add_plugin_link() {
        global $CFG;

        $output = $this->output->heading(get_string("showplugins", "local_ltsauthplugin"), 4);
         $links = array();

        $additemurl = new \moodle_url('/local/ltsauthplugin/subscriptions/manageplugins.php', array());
        $links[] = \html_writer::link($additemurl, get_string('addnewplugin', 'local_ltsauthplugin'));

        return $this->output->box($output . '<p>' . implode('</p><p>', $links) . '</p>', 'generalbox firstpageoptions');
    }

    /**
     * Return the html table of plugins
     *
     * @param plugin_exporter[] $plugins
     * @return string html of table
     */
    public function show_plugins_list($plugins) {
        global $DB;

        if (!$plugins) {
            return $this->output->heading(get_string('noplugins', 'local_ltsauthplugin'), 3, 'main');
        }

        $table = new \html_table();
        $table->id = 'local_ltsauthplugin_pluginsitempanel';
        $table->head = array(
            'ID',
            get_string('ltspluginname', 'local_ltsauthplugin'),
            get_string('note', 'local_ltsauthplugin'),
            get_string('actions', 'local_ltsauthplugin')
        );
        $table->headspan = array(1, 1, 1, 2);
        $table->colclasses = array(
            'id', 'name', 'note', 'edit', 'delete'
        );

        // Loop through the items and add to table.
        foreach ($plugins as $plugin) {
            $item = $plugin->export($this->output);
            $row = new \html_table_row();
            $row->cells = array();

            $itemidcell = new \html_table_cell($item->id);
            $row->cells[] = $itemidcell;

            $namecell = new \html_table_cell($item->name);
            $row->cells[] = $namecell;

            $notecell = new \html_table_cell($item->note);
            $row->cells[] = $notecell;

            $itemactionurl = '/local/ltsauthplugin/subscriptions/manageplugins.php';
            $itemediturl = new \moodle_url($itemactionurl, array('id' => $item->id));
            $itemeditlink = \html_writer::link($itemediturl, get_string('editplugin', 'local_ltsauthplugin'));
            $itemeditcell = new \html_table_cell($itemeditlink);
            $row->cells[] = $itemeditcell;

            $itemdeleteurl = new \moodle_url($itemactionurl, array('id' => $item->id, 'action' => 'confirmdelete'));
            $itemdeletelink = \html_writer::link($itemdeleteurl, get_string('deleteplugin', 'local_ltsauthplugin'));
            $itemdeletecell = new \html_table_cell($itemdeletelink);
            $row->cells[] = $itemdeletecell;

            $table->data[] = $row;
        }

        return \html_writer::table($table);
    }
}
