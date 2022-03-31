<?php

function getRequestParameter($key, $default = '')
{
    // If not request set
    if (!isset($_REQUEST[$key]) || empty($_REQUEST[$key])) {
        return $default;
    }

    // Set so process it
    return strip_tags((string) wp_unslash($_REQUEST[$key]));
}

function getEmailLoginButtons($arrEmails)
{
    $returnVal = '';
    foreach ($arrEmails as $item) {
        $returnVal .= "<li><button type='button' class='btn btn-primary btn-sm email-button' >";
        $returnVal .= $item.'</button></li>';
    }

    return $returnVal;
}

function domains_role_list()
{
    $arr_ret = [];
    $editable_roles = get_editable_roles();

    foreach ($editable_roles as $role => $details) {
        array_push($arr_ret, esc_attr($role));
    }

    return $arr_ret;
}

function domains_emails_list()
{
    $table_values = (get_option('circleauth_user_roles'));
    $objs = json_decode($table_values);

    return $objs;
}

function getOptionValue($opt)
{
    if ($opt != '') {
        $options = (get_option('circleauth-plugin-settings-group'));

        return $options[$opt];
    }
}

function getEmailAllowed($eml)
{
    $domains = domains_emails_list();
    var_dump(($domains));
    die();

    $email_domain = array_pop(explode('@', $eml));
    $retval = '';
    $especific = '';

    foreach ($domains as $key => $jsons) {
        $domains = ($jsons->domains);

        if (strpos($domains, $eml) > -1) {
            $especific = $jsons->role;
        }

        if (stripos($domains, 'All') > -1) {
            $retval = $jsons->role;
        }

        if ($especific == '') {
            if (strpos($domains, $email_domain) > -1) {
                $retval = $jsons->role;
            }
        }
    }

    return ($especific != '') ? $especific : $retval;
}

function userExists($circleAuthUserID)
{
    global $wpdb;
    $arrEmails = [];

    $tableName = $wpdb->prefix.'circleauth';
    $row = $wpdb->get_results('SELECT email FROM '.$tableName.' where unic_id="'.$circleAuthUserID.'"');

    foreach ($row as $item) {
        if ($item->email !== '') {
            array_push($arrEmails, $item->email);
        }
    }

    return $arrEmails;
}

function userLogin($email)
{
    if (!isset($email)) {
        return;
    }
    $url = (get_option('circleauth_redirect_page') != '') ? get_option('circleauth_redirect_page') : get_home_url();

    $user = get_user_by('email', $email);
    if (isset($user)) {
        do_action('wp_login', $user->user_login, $user->user_email);
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);
        wp_redirect($url);
    }
}

function decodeEmail($hash)
{
    if ($hash != '') {
        return base64_decode(preg_replace('/\s+/', '+', $hash));
    }
}

function checkDomainNewUser($email)
{
    $domains = get_option('circleauth_user_roles');

    $jsonUserDomains = json_decode($domains, true);
    $userRole = '';

    foreach ($jsonUserDomains as $item) {
        if (strpos($email, $item['domains']) !== false) {
            $userRole = $item['role'];
        }
    }
    echo $userRole;

    if (stripos($domains, $email_domain)) {
        return true;
    }

    return false;
}

function generatePassword()
{
    $bytes = openssl_random_pseudo_bytes(2);
    $password = md5(bin2hex($bytes));

    return $password;
}

function getUserRole($email)
{
    return get_option('new_user_domains_1_role');
}

function logDev($msg)
{
    $fh = fopen('php://stdout', 'w');
    fwrite($fh, $msg."\n");
    fclose($fh); //closing handler
}

function getEmailDomainAllowedRole($eml)
{
    $domains = domains_emails_list();

    if ($domains == '') {
        $domains = json_decode('"role":"subscriber","domains": "All" }');
    }

    $email_domain = array_pop(explode('@', $eml));

    $retval = '';
    $especific = '';

    foreach ($domains as $key => $jsons) {
        $domains = ($jsons->domains);

        if (strpos($domains, $eml) > -1) {
            $especific = $jsons->role;
        }

        if (stripos($domains, 'All') > -1) {
            $retval = $jsons->role;
        }

        if ($especific == '' && $email_domain != '') {
            if (strpos($domains, $email_domain) > -1) {
                $retval = $jsons->role;
            }
        }
    }

    return ($especific != '') ? trim($especific) : trim($retval);
}

function addcircleauthUser($circleauthUserID, $customId)
{
    global $wpdb;

    //decode the email
    $email = decodeEmail($customId);
    $newUserID = 0;
    $newWpUserID = 0;

    //check if the email or domain is allowed to access and
    //return WP access role
    $emailRole = getEmailDomainAllowedRole($email);

    if ($email == '') {
        return;
    }

    if (trim($emailRole) != '') {
        //check if the user exists on WP
        $user = get_user_by('email', $email);

        if (!empty($user)) {
            $newWpUserID = ($user->data->ID);
        } else {
            $password = generatePassword();

            $newWpUserID = wp_insert_user([
                'user_login' => $email,
                'user_pass' => $password,
                'user_email' => $email,
                'first_name' => '',
                'user_registered' => date('Y-m-d H:i:s'),
                'role' => $emailRole,
            ]);
        }
        //save the user in the circleauth table
        $tableName = $wpdb->prefix.'circleauth';
        $sql = 'insert into '.$tableName.' (user_id,unic_id,email) values(%s,%s,%s)';
        $query = $wpdb->prepare($sql, $newWpUserID, $circleauthUserID, $email);

        $wpdb->query($query);

        if ($wpdb->show_errors() == '') {
            userLogin($email);
        }
    } else {
        redirectEmailDomainNotAllowed($email);
    }

    return $email;
}

function redirectEmailDomainNotAllowed($email)
{
    $_SESSION['login_msg'] = "<strong>Error:</strong> Your Email (<strong>$email</strong>) or Email Domain is Not Allowed to Log in with Circle Access";
    $login_url = wp_login_url($redirect_page, true);

    wp_redirect($login_url);
}

function removeTable($table)
{
    global $wpdb;
    $table_name = $wpdb->prefix.$table;
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}

function createTable()
{
    global $wpdb;
    $table_name = $wpdb->prefix.'circleauth';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        user_id varchar(20) DEFAULT ''  NULL,
        unic_id varchar(100) DEFAULT '' NULL,
        email varchar(100) DEFAULT '' NULL,
        PRIMARY KEY  (id)
   ) $charset_collate;";

    dbDelta($sql);
}

function getTableContent($table)
{
    global $wpdb;
    $row = $wpdb->get_results('SELECT * FROM '.$table);

    foreach ($row as $item) {
        print_r($item);
    }
}

function getTables()
{
    global $wpdb;
    $tables = $wpdb->get_results('SHOW TABLES');
    foreach ($tables as $table) {
        foreach ($table as $t) {
            echo $t.'<br>';
        }
    }
}

function show($text)
{
    echo '<pre>';
    echo $text;
    echo '<br>';
    echo '</pre>';
}
