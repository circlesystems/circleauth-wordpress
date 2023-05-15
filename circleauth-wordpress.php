<?php

/**
 * @version 1.3.2
 */
/*
Plugin Name:  Circle Access
Plugin URI: https://github.com/circlesystems/circleauth-wordpress/
Description: Circle Access Authentication for WordPress
Author: Circle Systems
Version: 1.3.2
Author URI: http://circleaccess.circlesecurity.ai
*/

class CircleAuth
{
    private static $circleauth_instance;

    private function __construct()
    {
        $this->constants(); // Defines any constants used in the plugin
        $this->init();   // Sets up all the actions
    }

    public static function getInstance()
    {
        if (!self::$circleauth_instance) {
            self::$circleauth_instance = new CircleAuth();
        }

        return self::$circleauth_instance;
    }

    private function constants()
    {
        define('CIRCLEAUTH_VERSION', '1.3.2');
        define('CIRCLEAUTH_PATH', dirname(__FILE__));
        define('CIRCLEAUTH_CONSOLE_URL', 'https://console.circlesecurity.ai/');
        define('CIRCLEAUTH_LOGIN_URL', 'https://circleaccess.circlesecurity.ai/login/');
        define('CIRCLEAUTH_DOMAIN', 'https://circleaccess.circlesecurity.ai/');
        define('CIRCLEAUTH_EMAIL_INFO', 'support@circlesecurity.ai');
    }

    private function init()
    {
        //Add the menu page
        add_action('admin_menu', [$this, 'circleauth_plugin_create_menu']);

        //call register settings function
        add_action('admin_init', [$this, 'register_circleauth_plugin_settings']);

        //adds the Circle Access login button to the default login page
        add_action('login_form', [$this, 'sign_in_with_circleauth'], 10, 1);

        //removes the user from Circle Access table
        add_action('delete_user', function ($user_id, $blog_id) {
            $this->deleteCircleAuthUser($user_id);
        }, 10, 2);

        //shows messages in the default login page
        add_filter('login_message', [$this, 'loginMessage']);

        //call the routine to create the Circle Access table on plugin registration
        register_activation_hook(__FILE__, [$this, 'createCircleAuthTable']);

        //call the routine on plugin uninstall
        //register_uninstall_hook(__FILE__, [$this, 'uninstallCircleAuth']);

        $this->registerStylesScripts();
    }

    public function registerStylesScripts()
    {
        wp_register_style('tagify', plugins_url('admin/css/tagify.css', __FILE__));
        wp_enqueue_style('tagify');
 
        wp_register_style('query-confirm', plugins_url('admin/css/jquery-confirm.css', __FILE__));
        wp_enqueue_style('query-confirm');

        wp_register_style('circleButtons', plugins_url('admin/css/circleaccess-ui.css', __FILE__));
        wp_enqueue_style('circleButtons');

        wp_register_style('styles', plugins_url('admin/css/style.css', __FILE__));
        wp_enqueue_style('styles');

        wp_enqueue_script('jquery');

    }

    public function sign_in_with_circleauth()
    {
        if (get_option('circleauth_add_login_btn') == 'on') {
            ?>
      <script>
           var $ = jQuery.bind({});

            $(document).ready(function() {
            let obj = $("#loginform").children("p.submit");
            let buttonHtml = '<div  class="circleAuthLoginCont" style="text-align: center;width:100%;color:white;margin-bottom:15px;margin-top:50px">';
            buttonHtml +='<div style="margin-bottom: 18px;color:darkgrey"> OR </div>';
            buttonHtml +='<button id="unic-login" class="circleaccess-button circleaccess-button-light" onclick="circleAuthLogin(event)">';
            buttonHtml +='<span class="circleaccess-icon-wrapper"><img class="circleaccess-icon-login-btn" alt="" src="<?php echo plugin_dir_url(__FILE__).'/admin/images/circle_logo_only.svg'; ?>"/></span>';
            buttonHtml +='<span class="circleaccess-text circleaccess-text-long">Login with Circle </span></button>';
             buttonHtml +='</div>';
 
            $(obj).after(buttonHtml);
        }); 
      
         function circleAuthLogin(event){
            event.preventDefault();
            window.location.href= "<?php echo CIRCLEAUTH_DOMAIN.'/login/'.get_option('circleauth_app_key'); ?>";
         }
       </script>
       <?php
        } ?>

     <?php
    }

    public function loginMessage()
    {
        $msg = '';
        if(isset($_SESSION['login_msg'])){
            $msg = $_SESSION['login_msg'];
        }
        $_SESSION['login_msg'] = '';

        if ($msg != '') {
            return "<p class='message'>".$msg.'</p>';
        }
    }

    public function deleteCircleAuthUser($user_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'circleauth';
        $wpdb->delete($table_name, ['user_id' => $user_id]);
    }

    public function createCircleAuthTable()
    {
        // WP Globals
        global  $wpdb;

        $table_name = $wpdb->prefix.'circleauth';
        $charset_collate = $wpdb->get_charset_collate();

        // Create circleAuth Table if not exist
        if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
            // Query - Create Table
            $sql = "CREATE TABLE $table_name (
               id mediumint(9) NOT NULL AUTO_INCREMENT,
               time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
               user_id varchar(20) DEFAULT ''  NULL,
               unic_id varchar(100) DEFAULT '' NULL,
               email varchar(100) DEFAULT '' NULL,
               PRIMARY KEY  (id)
          ) $charset_collate;";
            require_once ABSPATH.'wp-admin/includes/upgrade.php';
            // Create Table
            dbDelta($sql);
        }
    }

    public function circleauth_plugin_create_menu()
    {
        //create new top-level menu
        add_menu_page('Circle Access Settings', 'Circle Access', 'administrator', __FILE__, [$this, 'circleauth_plugin_settings_page'], plugins_url('/admin/images/circle_logo_19.svg', __FILE__));
    }

    public function register_circleauth_plugin_settings()
    {
        //register our settings
        register_setting('circleauth-plugin-settings-group', 'circleauth_app_key');
        register_setting('circleauth-plugin-settings-group', 'circleauth_app_read');
        register_setting('circleauth-plugin-settings-group', 'circleauth_app_write');
        register_setting('circleauth-plugin-settings-group', 'circleauth_add_login_btn');
        register_setting('circleauth-plugin-settings-group', 'circleauth_redirect_page');
        register_setting('circleauth-plugin-settings-group', 'circleauth_callback_page');
        register_setting('circleauth-plugin-settings-group', 'circleauth_user_roles');
        register_setting('circleauth-plugin-settings-group', 'circleauth_auto_register_new_user');
        register_setting('circleauth-plugin-settings-group', 'circleauth_redirect_new_user_page');
         
    }

    public function circleauth_plugin_settings_page()
    {
        require_once CIRCLEAUTH_PATH.'/admin/templates/header.php'; ?>

        <div class="wrap">
        <?php settings_errors(); ?>

        <form method="post" action="options.php">
        <?php
            settings_fields('circleauth-plugin-settings-group');
            do_settings_sections('circleauth-plugin-settings-group');

            require_once CIRCLEAUTH_PATH.'/api/circleauth.php';
            require_once CIRCLEAUTH_PATH.'/includes/functions.php';
            require_once CIRCLEAUTH_PATH.'/admin/templates/subheader.php';
            require_once CIRCLEAUTH_PATH.'/admin/templates/main.php'; ?>
            </form>
        </div>

    <?php
    }
}

 session_start();
 $circleAuth = CircleAuth::getInstance();

?>
