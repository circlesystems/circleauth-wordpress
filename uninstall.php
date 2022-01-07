<?php

try {
    global $wpdb;
    $table_name = $wpdb->prefix.'circleauth';
    $sql = 'drop table '.$table_name;
    $wpdb->query($sql);
} catch (Exception $e) {
}

try {
    delete_option('circleauth_app_key');
    delete_option('circleauth_app_read');
    delete_option('circleauth_app_write');
    delete_option('circleauth_add_login_btn');
    delete_option('circleauth_redirect_page');
    delete_option('circleauth_callback_page');
    delete_option('circleauth_user_roles');
} catch (Exception $e) {
}
