<?php
defined('ABSPATH') || die();
?>
 
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&display=swap" rel="stylesheet">
  
    <header >

    <div class="circleaccess-navbar-header">

       <div style="width:68%;display:flex">
         <img style="padding-left:56px;height:36px;padding-top:16px" src="<?php echo plugin_dir_url(dirname(__FILE__)).'images/circle_logo_only.svg'; ?>"/>
         <span style="font-size:18px;color:white;font-face:Nunito Sans;margin-top:25px;margin-left:20px">Circle Access</span>
       </div>
        
       <div style="width:8%;vertical-align:midle">
         <a class="circleacces-nav-link" target="_blank" href="mailto:<?php echo CIRCLEAUTH_EMAIL_INFO; ?>">Contact</a>
       </div>
       <div style="width:15%">
         <a class="circleacces-nav-link" target="_blank" href="<?php echo CIRCLEAUTH_DOMAIN; ?>docs/">Documentation</a>
       </div>

    </div>

  </header>
  