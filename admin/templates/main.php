<?php
 include_once '../includes/functions.php';
 define('REMOVE_ICON', plugin_dir_url(dirname(__FILE__)).'/images/remove.svg');
?> 
 
<script>
  var remove_icon = "<?php echo REMOVE_ICON; ?>";
  var wp_roles = '<?php echo json_encode(domains_role_list()); ?>';
  var domain_roles = '<?php echo json_encode(domains_emails_list()); ?>'; 
</script>

<script src="<?php echo plugins_url('../js/jquery.min.js', __FILE__); ?>"></script>
<script src="<?php echo plugins_url('../js/tagify.min.js', __FILE__); ?>"></script>
<script src="<?php echo plugins_url('../js/bootstrap.bundle.min.js', __FILE__); ?>"></script>
<script src="<?php echo plugins_url('../js/jquery-confirm.min.js', __FILE__); ?>"></script>
<script src="<?php echo plugins_url('../js/highlight.pack.min.js', __FILE__); ?>"></script>
<script src="<?php echo plugins_url('../js/clipboard.min.js', __FILE__); ?>"></script>
<script src="<?php echo plugins_url('../js/tooltips.js', __FILE__); ?>"></script>
<script src="<?php echo plugins_url('../js/main.js', __FILE__); ?>"></script>

<table class="form-table"  >
   <tbody>
      <tr>
         <th scope="row"><label for="appkey"><?php _e('App Key', 'circleauth-wordpress'); ?>
            - <em>(<?php _e('Required', 'circleauth-wordpress'); ?>)</em></label>
         </th>
         <td>
            <input name="circleauth_app_key" type="text" id="appkey"
               value="<?php echo esc_attr(get_option('circleauth_app_key')); ?>" class="regular-text circleacces-input-text">
               
            <p class="description"
               id="tagline-appkey"><?php printf(__('Application %1$s available at <a target="blank" href="%2$s">%3$s Console</a>', 'circleauth-wordpress'), 'App Key', CIRCLEAUTH_CONSOLE_URL, 'Circle Access'); ?></p>
         </td>
      </tr>
      <tr>
         <th scope="row"><label for="readkey"><?php _e('Read Key', 'circleauth-wordpress'); ?>
            - <em>(<?php _e('Required', 'circleauth-wordpress'); ?>)</em></label>
         </th>
         <td>
            <input name="circleauth_app_read" type="text" id="readkey"
               value="<?php echo esc_attr(get_option('circleauth_app_read')); ?>"  class="regular-text circleacces-input-text">
               
            <p class="description"
               id="tagline-readkey"><?php printf(__('Application %1$s available at <a target="blank" href="%2$s">%3$s Console</a>', 'circleauth-wordpress'), 'Read Key', CIRCLEAUTH_CONSOLE_URL, 'Circle Access'); ?></p>
         </td>
      </tr>

      <tr>
         <th scope="row"><label for="writekey"><?php _e('Write Key', 'circleauth-wordpress'); ?>
            - <em>(<?php _e('Required', 'circleauth-wordpress'); ?>)</em></label>
         </th>
         <td>
            <input name="circleauth_app_write" type="text" id="writekey"
               value="<?php echo esc_attr(get_option('circleauth_app_write')); ?>" class="regular-text circleacces-input-text">
               
            <p class="description"
               id="tagline-writekey"><?php printf(__('Application %1$s available at <a target="blank" href="%2$s">%3$s Console</a>', 'circleauth-wordpress'), 'Write Key', CIRCLEAUTH_CONSOLE_URL, 'Circle Access'); ?>
            </p>
         </td>
      </tr>
  
      <tr style="display:none">
         <th scope="row"><label for="user_roles"><?php _e('', 'user_roles'); ?></label>
         </th>
         <td>
            <input name="circleauth_user_roles" type="text" id="user_roles"
               value="<?php echo esc_attr(get_option('circleauth_user_roles')); ?>" class="regular-text circleacces-input-text">
         </td>
      </tr>
 
      <tr>
         <th scope="row"><label for="redirect"><?php _e('Redirect page after login', 'circleauth-wordpress'); ?>
            - <em>(<?php _e('Optional', 'circleauth-wordpress'); ?>)</em></label>
         </th>
         <td>
            <input name="circleauth_redirect_page" type="text" id="redirect_page"
               value="<?php echo get_option('redirect_page') ? esc_attr(get_option('circleauth_redirect_page')) : get_home_url(); ?>"  class="regular-text circleacces-input-text">
            <p class="description"
               id="tagline-readkey"><?php echo __('Page to be redirected after login '); ?></p>
         </td>
      </tr>
      <tr>
         <th scope="row"><label for="redirect"><?php _e('Circle Access callback page', 'circleauth-wordpress'); ?>
             </label>
         </th>

         <td>
            <?php $call_back_page = get_home_url().'/wp-content/plugins/circleaccess-wordpress-main/callback.php'; ?>

            <input name="circleauth_callback_page" readonly type="text" id="callback_page"
               value="<?php echo $call_back_page; ?>"  class="regular-text circleaccess-callback-input">
               
               <button class="btn" style="border: none" type="button" data-toggle="tooltip" title="Copy to Clipboard!" data-clipboard-callback data-clipboard-target="#callback_page">
                 <img class="clippy" src="<?php echo plugin_dir_url(dirname(__FILE__)).'images/clippy.svg'; ?>" width="13" alt="Copy to clipboard">
                 <span id="copied" class="circleaccess-copied" >Copied!</span>
               </button>

               <p class="description"
                  id="tagline-readkey">
                  <?php printf(__('Callback page to be added %1$s at <a target="_blank" href="%2$s">%3$s Console</a>', 'circleauth-wordpress'), '', CIRCLEAUTH_CONSOLE_URL, 'Circle Access'); ?>
               </p>   
          </div>
         </td>
      </tr> 
      <tr>
         <th scope="row"><label for="domains"><?php _e('Circle Access can be configured to accept new users from specific domains or emails. You can also set the WordPress role. ', 'domains'); ?></label>
         </th>
         <td >
            
            <div id="sell" style="margin-top: 9px;"></div>
            <div style="display: flex;margin-top:8px">
               <div>
                    <input type="button" class="button-primary" onclick="add_user_role()" value="<?php echo __('Add New User Role.'); ?>"/>
               </div>
               <div style="padding-left: 15px;">
                  <span class="description">
                     <?php printf(__('Use the tag <strong>All</strong> to accept new users from any domain.')); ?>
                  </span>
               </div>
           </div>
         </td>
      </tr>
      <tr>
         <th scope="row"><label for="readkey"><?php _e('Enable login default page', 'circleauth-wordpress'); ?>
            </em></label>
         </th>

          <td>
          <input type="checkbox" class="form-check-input" <?php if (get_option('circleauth_add_login_btn') == 'on') {
    echo 'checked';
    } ?> name="circleauth_add_login_btn" id="add_login_btn"> <?php echo __('Add Circle Access login to default WordPress login page'); ?>
 
          </td>
      </tr>
   </tbody>
</table>
 

<?php submit_button(); ?>
