<?php
if (!defined('WP_UNINSTALL_PLUGIN')) { exit; }
$role = get_role('administrator');
if ($role) {
    $role->remove_cap('fm_gifts_control_manage');
    $role->remove_cap('fm_gifts_control_approve');
}
delete_option('fm_gifts_control_version');
