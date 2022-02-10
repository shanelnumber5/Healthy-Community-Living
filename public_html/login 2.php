<?php
/**
 * Login Page used only for FTP published sites.
 * As a result, no external library is allowed.
 */

// Configuration variables
$cookieName = 'WeeblySiteLogin';
$scriptLocation = "/login.php";

$uri  = $_SERVER['REQUEST_URI'];
$host = $_SERVER['HTTP_HOST'];
$referer = $_SERVER['HTTP_REFERER'];

if ($_POST['redirect']) {
	// User submit login form, process and prepare authentication cookie for destination page to authenticate.

	// Set cookie with authentication credentials
	$passwordSecret = '';
	setrawcookie($cookieName, "weeblylogin:" . hash_hmac("sha256", $_POST['p'], $passwordSecret), time()+(60*60*24*30), '/');

	// adapt protocol
	$protocol='http';
	if (isset($_SERVER['HTTPS'])) {
		if (strtoupper($_SERVER['HTTPS']) == 'ON') {
			$protocol = 'https';
		}
	}

	// Prevent off-site redirect
	$redirect = preg_replace("/^\//", "", $_POST['redirect']);
	$redirect = str_replace("\n", "", $redirect);

	// redirect to destination page with authentication cookie prepared
	header("Location: {$protocol}://".$host."/".$redirect);

} elseif ($_GET['redirect']) {
	// Authentication fails, show login page again to let user retry password.

	// Show login page
	showLogin($cookieName);

} else {
	// Redirect to real script location, passing the current URI as a parameter
	header("Location: ".$scriptLocation."?redirect=$uri");

}

/**
 * @param string $cookieName
 */
function showLogin($cookieName) {
	$redirectUrl = strip_tags( $_GET['redirect'] );
	$messageTextColor = '#959595';
	$loginErrorMessage = '';
	if (isset($_COOKIE[$cookieName])) {
		$messageTextColor = "red";
		$loginErrorMessage = "<p class='error-message'>Invalid username or password, please try again.</p>";
	}


	echo <<<EOT
<html>
<head><title>This area is password protected</title>
	<style type="text/css">
		#login {
			float: none;
			text-align: left;
			width: 410px;
			margin: 0px auto;
			margin-top: 134px;
			background: #171717;
			border: 4px solid #222222;
			font-family: arial;
			color: white;
			padding: 0 0 15px 25px;
			opacity: .85;
			filter: alpha(opacity=85);
		}

		#title {
			font-size: 24px;
			font-weight: bold;
			display: block;
			width: 385px;
			border-bottom: 1px solid #888;
			margin-bottom: 30px;
		}

		#submit {
			background: #E9E9E9;
			color: #161616;
			font-size: 18px;
			font-weight: bold;
			padding: 4px;
			margin-left: 5px;
		}

		#p {
			border: 2px solid {$messageTextColor};
			font-size: 18px;
			padding: 5px;
			width: 305px;
		}

		.error-message {
			font-size: 14px;
			color: red;
		}

	</style>
	<!--[if IE]>
	<style type="text/css">

	#login {
		padding: 25px 25px 15px 25px;

	}

	#p {
		width: 270px;
		height: 35px;
	}

	#submit {
		padding: 0px;
		margin-left: 5px;
		height: 38px;
		position: relative;
		top: 2px;
	}

	</style>
	<! [endif]-->

</head>
<body style='background: #F2F2F2; text-align: center; margin: 0; padding: 0;' onload="document.getElementById('p').focus()">
<div id="login">

	<p id='title'>This area is password protected</p>
	<form method="POST">
		<p style='font-size: 14px;'>Please enter the password below</p>
		<input type='password' name='p' id='p'/>
		<input type='submit' id='submit' value='Login'/>
		<input type='hidden' name='redirect' value='{$redirectUrl}'/>
		<input type='hidden' name='u' value='weebs'/>
		{$loginErrorMessage}
	</form>


</div>
</body>
</html>
EOT;

}
