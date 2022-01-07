<?php
defined('ABSPATH') || die();
?>
 
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&display=swap" rel="stylesheet">
  
    <header >
    <nav class="navbar navbar-expand-md navbar-dark" style="background-color: black">
        <div class="container">

            <a class="navbar-brand" style="text-decoration: none;" href="../"><img src="<?php echo plugin_dir_url(dirname(__FILE__)).'images/logo.svg'; ?>" alt="Circle Auth" class="logo" style="width:30px;margin-top: -15px;">
                Circle Auth</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav" >
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" target="_blank" href="mailto:<?php echo CIRCLEAUTH_EMAIL_INFO; ?>">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" target="_blank" href="<?php echo CIRCLEAUTH_DOMAIN; ?>docs/">Documentation</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
  </header>
  