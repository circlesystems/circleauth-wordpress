<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

require_once dirname(__FILE__).'/api/circleauth.php';
require_once dirname(__FILE__).'/includes/functions.php';
require_once '../../../wp-load.php';

//get the CircleAuth stored keys
$GLOBALS['api_appKey'] = get_option('circleauth_app_key');
$GLOBALS['api_readKey'] = get_option('circleauth_app_read');
$GLOBALS['api_writeKey'] = get_option('circleauth_app_write');

session_start();

$userID = getRequestParameter('userID');
$sessionID = getRequestParameter('sessionID');
$type = getRequestParameter('type');
$customId = getRequestParameter('customID');
$selectedEmail = getRequestParameter('login');
$email = decodeEmail(getRequestParameter('customID'));
$auto_register = get_option('circleauth_auto_register_new_user');
$new_user_redirect_page = get_option('circleauth_redirect_new_user_page') ? get_option('circleauth_redirect_new_user_page') : get_home_url();

// save session with userID and sessionID
$_SESSION['userID'] = $userID;
$_SESSION['sessionID'] = $sessionID;
$_SESSION['type'] = $type;

// check if the auto register is not enabled and if the user is already registered
if (!$auto_register){
  $sessionData = getSession($_REQUEST['sessionID']);
  $hashedEmails = $sessionData['data']['userHashedEmails'];

  $emailFromWP = getWPUserEmail($hashedEmails);
  if ($emailFromWP){
    userLogin($emailFromWP);
  }else{
    header('location:'.$new_user_redirect_page);
  }
}else{
  $userEmail = userExists($userID);

  if (trim($userEmail[0]) != '') {
      //check if user is allowed to access
      if (sizeof($userEmail) === 1 || (!empty($selectedEmail))) {
          userLogin((!empty($selectedEmail) ? $selectedEmail : $userEmail[0]));
      }
  } elseif ((!isset($customId)) || ($customId == '')) {
      //get the session data and store it in the session
      $_SESSION['session_data'] = getSession($_REQUEST['sessionID']); 
      header('location:'.CIRCLEAUTH_CONSOLE_URL.'dashboard/login_email/index?appKey='.$api_appKey);
  }

  // check if it´s the same user 
  $userIDFromRequest = $_REQUEST['userID'];
  $userIDFromSession = $_SESSION['session_data']['data']['userID'];

  if ($userIDFromRequest == $userIDFromSession){
    //check if the email is valid
      $hashedEmails = $_SESSION['session_data']['data']['userHashedEmails'];
      if (in_array(hash('sha256',$email),$hashedEmails)){
      $user = addCircleAuthUser($userID, $customId);
    }
  }
  die("Authentication failed. Please, try again.");
} 
 
?>

<html lang="en_US">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Master Curcio">
    <link rel="icon" href="admin/images/logo128.png" type="image/png"/>
    <link rel="apple-touch-icon" href="admin/images/logo128.png" type="image/png"/>
    <meta name="description" content="Circle Access. No more phishing. Secure 2FA. Simple Login">
    <meta name="keywords" content="Circle Access, No phishing,Secure 2FA, Simple Login, simple auth, simple login, passwordless login, no username login">
    <title>Circle Access</title>

    <!-- Bootstrap core CSS -->
 
    <script src="admin/js/jquery.min.js"></script>
    
   <!-- Custom styles for this template -->
    <style>
    html {
        font-size: 14px;
    }
    @media (min-width: 768px) {
        html {
            font-size: 16px;
        }
    }

    .container {
        max-width: 960px;
    }

    .pricing-header {
        max-width: 700px;
    }

    .card-deck .card {
        min-width: 220px;
    }

    #qr-code {
      min-height: 250px;
      min-width: 250px;
    }

    .badge-download {
      height: 50px;
      margin: 15px;
    }

    .border-top { border-top: 1px solid #e5e5e5; }
    .border-bottom { border-bottom: 1px solid #e5e5e5; }

    .box-shadow { box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05); }
   
    .email-button{
        margin-bottom: 10px;
        min-width: 250px;
      }
  
   </style>

  </head>
  <body>
    <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-3 bg-white border-bottom box-shadow">
      <h5 class="my-0 mr-md-auto font-weight-normal"><center><img src="admin/images/logo128.png" style="width: 30px; height: auto;"></center></h5>
    </div>
    <div class="container">
      <div style="height:20px;">&nbsp;</div>
      <div class="card-deck mb-3 text-center">
        <div class="card mb-4 box-shadow">
          <div class="card-body">
            <h1 class="card-title pricing-card-title" id="artigoTitulo"><?php echo __('Login'); ?></h1>
            <ul class="list-unstyled mt-3 mb-4">
              <li>
                <br><h6>
                There is more than one email registered for this device. <br>Please, select one to log in
                </h4>
              </li>
         
              <li>&nbsp;</li>
              <li>
                <?php echo getEmailLoginButtons($userEmail); ?>
             </ul> 
          </div>
        </div>
      </div>
    </div>
    <footer class="pt-4 my-md-5 p-3 pt-md-5">
      <div class="row">
        <div class="col-12 col-md">
          <img src="admin/images/logo128.png" alt="" width="24px" height="24px">
          <small class="text-muted">&copy; 2020-2021
            <br>
          </small>
        </div>
      </div>
    </footer>
   
    <script>

      $(function() {
      
        $(".email-button").on("click",function(){
          if ($(this).text() != ""){
            location.href=location.href+"&login=" + $(this).text();
          }
        });

      });

    </script>
 
  </body>
</html>
