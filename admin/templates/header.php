<?php
defined('ABSPATH') || die();
?>
 
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&display=swap" rel="stylesheet">
  
    <header style="background-color:black;margin-left:-23px">

    <div class="circleaccess-navbar-header">

       <div class="product">
         <img  src="<?php echo plugin_dir_url(dirname(__FILE__)).'images/circle_logo_only.svg'; ?>"/>
         <span class="title">Circle Access</span>
       </div>
        
       <div style="width:8%;vertical-align:midle">
         <a class="circleacces-nav-link" target="_blank" href="mailto:<?php echo CIRCLEAUTH_EMAIL_INFO; ?>">Contact</a>
       </div>
       <div style="width:15%">
         <a class="circleacces-nav-link" target="_blank" href="<?php echo CIRCLEAUTH_DOMAIN; ?>docs/">Documentation</a>
       </div>

    </div>

  </header>
  