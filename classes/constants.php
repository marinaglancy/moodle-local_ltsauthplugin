<?php
/**
 * Created by PhpStorm.
 * User: ishineguy
 * Date: 2018/05/12
 * Time: 22:46
 */

namespace local_ltsauthplugin;

defined('MOODLE_INTERNAL') || die();

class constants {
    const USERSITE_TABLE = 'local_ltsauthplugin_usersite';
    const USER_TABLE = 'local_ltsauthplugin_users';
    const SUB_TABLE = 'local_ltsauthplugin_subs';
    const APP_TABLE = 'local_ltsauthplugin_apps';
    const USERSUB_TABLE = 'local_ltsauthplugin_usersubs';
    const AWSACCESSID_NONE = 'noaccessid';
    const AWSACCESSSECRET_NONE = 'noaccesssecret';
}