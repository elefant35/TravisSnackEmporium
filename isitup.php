<?php
//globals------------------------------------------------------------------------------
$Wurl= "https://hooks.slack.com/services/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";
$Filename = "records.csv";
//functions----------------------------------------------------------------------------

//sends an array to slack to be displayed
function sendPackage($data, $Wurl){
	$ch = curl_init($Wurl);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	curl_exec($ch);
	curl_close($ch);


}
function makePackage($string)
{	
	$data= "payload=" . json_encode(array(
	"channel" => "#{$_POST['channel_name']}",
	"text" => $string));


	return $data;
}
function getFunds($name, $csv)
{
	foreach($csv as $acsv){
		if($acsv['name'] == $name){
			$fund = $acsv['funds'];	
		}
	}
	return $fund;
}
function subtractFunds(&$csv, $Filename, $amount, $name)
{
	foreach($csv as &$acsv){
		if( $acsv['name'] == $name){
			$acsv['funds'] = $acsv['funds'] - $amount; 
		}
	}
	$fp = fopen($Filename, 'w');
	fputs($fp,"name,funds\n");
	foreach($csv as $row){
		fputcsv($fp, $row);
	}
	fclose($fp);
}


//check token------------------------------------------------------

$token = $_POST['token'];

if($token !='U6dJ9dqCQwpEAaZLmTOvk9xh'){
	echo ('<img src= http://vignette1.wikia.nocookie.net/muppet/images/5/5b/Oscar-can.png/revision/latest?cb=20120117061845>');
	die("Go Away");
}

//get file ready--------------------------------------------------
$csv = array_map('str_getcsv', file($Filename));
array_walk($csv, function(&$a) use ($csv){
	$a = array_combine($csv[0],$a);
});
array_shift($csv);
//echo($csv[1]['funds']);

//MAIN-------------------------------------------------------------

if( $_POST['channel_name'] == "bot-testing"){
	//seperate words
	$text = explode(' ',$_POST['text']);
	//echo("$text[0]");
	
	//ADD FUNDS
	if($text[0] == "add"){
		echo("add");
	}


	//BUY ITEM
	if($text[0] == "buy"){
		echo("buy");
	}
	//SUBTRACT FUNDS
	if($text[0] == "subtract"){
		echo("subtract\n");
		subtractFunds($csv, $Filename,$text[1],$_POST['user_name']);
		$fund = getFunds($_POST['user_name'], $csv);
		echo("You have $fund left in your account\n");

	}
	//get Funds
	if($text[0] == "funds"){
		echo("funds");
		$fund = getFunds($_POST['user_name'], $csv);
		echo($fund);
	}
//	echo($_POST['channel_name']);

	//$data = makePackage("meh");

	//sendPackage($data, $Wurl);
}
else{
	//echo($_POST['channel_name']);
	die("command not allowed in this channel");
}

//closing------------------------------------
echo(" Now get out of my shop!");


?>

