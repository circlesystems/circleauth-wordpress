# Circle Auth
Circle Auth provides Continuous Authentication, Authorization API, Codeless 2FA, Credential-less Authentication , modules and apps to make the user access secure and easy.

# CircleAuth WordPress plugin

## Installation

### 1) Download the zip file

Go to the GitHub https://github.com/circlesystems/circleauth-wordpress, click on the "Code" button and select the "Download ZIP" option.

![alt text](docs/images/image20.png)


### 2) Go to “Plugins” on your WordPress dashboard

![alt text](docs/images/image1.png) 


Once you have your ZIP file, go back to your WordPress wp-admin panel, click on “Plugins” on the sidebar menu, and then on the “Add New” button.


### 3) Upload your plugin archive

From there, click on the “Upload Plugin” button visible at the top:


![alt text](docs/images/image25.png)

On the next screen, you will be able to upload your plugin’s ZIP file straight to your WordPress. Just select the ZIP from your computer and confirm the upload by clicking on “Install Now.”

### 4) Activate the plugin

![alt text](docs/images/image28.png)

After doing so, the plugin is online and you can start using it.

<hr>
 
## Configuration

To start using the plugin, some configurations are needed. 

![alt text](docs/images/image35.png)
 

### 1. App Key, Read Key and Write Key. 

![alt text](docs/images/image45.png)

You can retrieve the keys from the Circle Auth console (https://console.gocircle.ai/).
In this example, we register WordPress as an application named "WordPress 9000".
(Our WordPress runs locally on port 9000).
 
![alt text](docs/images/image50.png)

 
![alt text](docs/images/image55.png)

### 2. Redirect page after login

![alt text](docs/images/image58.png)

Optionally, you can register a page to which the user will be redirected after login.


### 3. Circle Auth callback page

![alt text](docs/images/image60.png)

This URL is used when registering the application on Circle Auth console.

![alt text](docs/images/image65.png)

### 4. Domains and e-mails

Circle Auth can be configured to only accept logins from specific domains or emails.
For example, you can specify that only logins whose email domain is the company's domain are accepted.
It is also possible to configure by domain or email what role the new user should take in WordPress. This rule is only available for users who are not yet registered with WordPress.

![alt text](docs/images/image70.png)

### 5. Enable login default page

When this option is enabled, the Circle Auth login button is added to the default WordPress login page.

![alt text](docs/images/image41.png)


