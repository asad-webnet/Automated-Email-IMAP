
<?php

ini_set('memory_limit','-1');
ini_set('max_execution_time', 0); 


function selectingTemplate($subject,$conn,$emailAddress) {



$sql="SELECT template_Name,email_template FROM templates WHERE template_Name LIKE '$subject%'";
$result=$conn->query($sql);

if (!$result) {
	trigger_error('Invalid query: ' . $conn->error);
}

if ($result->num_rows > 0) {

	// output data of each row
	while($row=$result->fetch_assoc()) {
		$template=$row['email_template'];
		$template_name=$row['template_Name'];

			$to      = $emailAddress;
			$subject = $subject;
			$message = $template;
				$headers .= 'From: <mitch@militarycruisedeals.com>' . "\r\n";
$headers .= 'Cc: '. $emailAddress . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";    
				
				if (mail($to, $subject, $message, $headers)) 
				{
				   $sql    = "INSERT INTO email_record (email_to, template_email) 
		 VALUES('$to', '$template_name')";
							
							if ($conn->query($sql) === TRUE) {
								echo "Email sending successfully to " . $to . " and the subject is ". $subject. "<br/>";
							} else {
								echo "Email couldn't be sent. Error: " . $sql . "<br>" . $conn->error;
							}

						   
				} 
				else 
				{
					echo("Message delivery failed...");
				}
			

	}
}

else {
	echo "No subject";
}
}




$servername = "localhost";
$username = "military_demo";
$password = "2Yx2KRHtfIRe";
$db="military_template";

// try {
//     $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
//     // set the PDO error mode to exception
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     echo "Connected successfully"; 
//     }
// catch(PDOException $e)
//     {
//     echo "Connection failed: " . $e->getMessage();
//     }


$conn = mysqli_connect($servername, $username, $password,$db);

// Check connection
if (!$conn) {
die("Connection failed: " . mysqli_connect_error());
}

// NOW SELECTING EACH EMAIL ADDRESS

$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'mitch@militarycruisedeals.com';
$password = 'rnPU8#M3hHQ';

/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

$sql="SELECT * FROM templates";
$result=$conn->query($sql);

if (!$result) {
	trigger_error('Invalid query: ' . $conn->error);
}

 while($row=$result->fetch_assoc()) 
 {
		$sub= $row['template_Name'];
		
		/* grab emails */
		//$emails = imap_search($inbox,'SUBJECT');
		$emails   = imap_search($inbox, 'SUBJECT "'.$sub.'" UNSEEN');

		//die;
		
	
		/* if emails are returned, cycle through each... */
		if($emails)
		{
			foreach($emails as $email_number) 
			{
				
				
				$headers = imap_headerinfo($inbox, $email_number);
				
				
				$customerEmail =  "Email: ".$headers->from[0]->mailbox . '@'.$headers->from[0]->host;
				
				$message = imap_fetchbody($inbox,$email_number, 1);
					// $message = explode("Email:",$message);
				$message  =  preg_replace('/\s+/u', '', $message);
				$message = str_replace('=', '', $message);
				$message = explode("Email:",$message);
				$message = explode("com",$message[1]);

				$emailAddress=$message[0].'com';
			
				selectingTemplate($sub,$conn,$emailAddress);
				///////////////////////////////////// database get the template ///////////////////////
				
	
	
			}
		}
		
		else
		{
			echo " Emails related to the specific subject not Found.";
		}
 }

	
/* close the connection */
imap_close($inbox);



mysqli_close($conn);
?>
