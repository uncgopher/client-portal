<?PHP 
require 'PHPMailerAutoload.php';

//Create a new PHPMailer instance
$phpmail = new PHPMailer;

/* certificates don't match on dreamhost, ignore error
$phpmail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);*/

// utf8
$phpmail->CharSet = 'UTF-8';

//Tell PHPMailer to use SMTP
$phpmail->isSMTP();

//Amazon SES requires TLS
$phpmail->SMTPSecure = 'tls';

// Use HTML
$phpmail->IsHTML(true);

//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages 
$phpmail->SMTPDebug = 0;

//Ask for HTML-friendly debug output
$phpmail->Debugoutput = 'html';

//Set the hostname of the mail server
$phpmail->Host = "email-smtp.us-east-1.amazonaws.com";

//Set the SMTP port number - likely to be 25, 465 or 587
$phpmail->Port = 587;

//Whether to use SMTP authentication
$phpmail->SMTPAuth = true;

//Username to use for SMTP authentication
$phpmail->Username = "AKIAJ6M47KKFBVUE3M7Q";

//Password to use for SMTP authentication
$phpmail->Password = "AuA3e/h7sA/rLdgu2xilpFE7AV5SRwQkgneMGCJ6l/OV";

//Set who the message is to be sent from
$phpmail->setFrom('support@justmystyleapp.com','JustMyStyle');

//Set an alternative reply-to address
$phpmail->addReplyTo('support@justmystyleapp.com');