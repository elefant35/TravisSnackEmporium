<?php
//globals------------------------------------------------------------------------------
$Wurl= "https://hooks.slack.com/services/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";
$Filename = "records2.csv";
$Filename2 = "snacks.csv";
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
function addFunds(&$csv, $Filename, $amount, $name)
{
	foreach($csv as &$acsv){
		if( $acsv['name'] == $name){
			$acsv['funds'] = $acsv['funds'] + $amount; 
		}
	}
	$fp = fopen($Filename, 'w');
	fputs($fp,"name,funds\n");
	foreach($csv as $row){
		fputcsv($fp, $row);
	}
	fclose($fp);
}
function addMe(&$csv, $Filename, $name)
{
	foreach($csv as &$acsv){
		if( $acsv['name'] == $name){
			echo("You have already been addedi\n");
			return 0;
		}
		}
		array_push($csv, array( "name" => $name, "funds" => 0)); 
		$fp = fopen($Filename, 'w');
		fputs($fp,"name,funds\n");
		foreach($csv as $row){
			fputcsv($fp, $row);
		}
		echo("Your account has been successfully created\n");
		fclose($fp);		
}
function menu($csv)
{
	foreach($csv as $acsv){
		echo($acsv['name'].': $'.$acsv['price']."\n");
	}

}
function buy(&$csv, $snackcsv, $Filename, $name, $snack)
{
	foreach($snackcsv as $acsv){
		if($acsv['name'] == $snack)
		{
			$price = $acsv['price'];
		}
	}
	if( $price == NULL)
	{
		echo("Snack not found");
	}
	else{
		echo("Snack purchased for ".'$'. "$price \n");
		subtractFunds($csv, $Filename, $price, $name);
	}


}

//functions - admin
function addToInven(&$csv, $snackcsv, $amount, $name)
{
	 echo("New snack $name , is this correct?")


//check token------------------------------------------------------

$token = $_POST['token'];

if($token !='U6dJ9dqCQwpEAaZLmTOvk9xh'){
	echo ('<img src= http://vignette1.wikia.nocookie.net/muppet/images/5/5b/Oscar-can.png/revision/latest?cb=20120117061845>');
	die("Go Away");
}

//get presonel csv file ready--------------------------------------------------
$csv = array_map('str_getcsv', file($Filename));
array_walk($csv, function(&$a) use ($csv){
	$a = array_combine($csv[0],$a);
});
array_shift($csv);

//get snack csv file ready
$snackcsv = array_map('str_getcsv', file($Filename2));
array_walk($snackcsv, function(&$b) use ($snackcsv){
	$b = array_combine($snackcsv[0],$b);
});
array_shift($snackcsv);

//echo($csv[1]['funds']);

//MAIN-------------------------------------------------------------

//if( $_POST['channel_name'] == "bot-testing"){
	//seperate words
	$text = explode(' ',$_POST['text']);
	//echo("$text[0]");
	
	//ADD FUNDS
	if($text[0] == "add"){
	//	echo("add");
		addFunds($csv, $Filename,$text[1],$_POST['user_name']);
		$fund = getFunds($_POST['user_name'], $csv);
		echo('You have $'."$fund left in your account \n");
		

	}


	//BUY ITEM
	if($text[0] == "buy"){
		buy($csv, $snackcsv,$Filename, $_POST['user_name'], $text[1]);
		$fund = getFunds($_POST['user_name'], $csv);
		echo('You have $'."$fund left in your account \n");

	}
	//SUBTRACT FUNDS
	if($text[0] == "subtract"){
	//	echo("subtract\n");
		subtractFunds($csv, $Filename,$text[1],$_POST['user_name']);
		$fund = getFunds($_POST['user_name'], $csv);
		echo('You have $'."$fund left in your account \n");
		

	}
	//get Funds
	if($text[0] == "funds"){
	//	echo("funds");
		$fund = getFunds($_POST['user_name'], $csv);
		echo('You have $'."$fund left in your account \n");
	}
	
	//setup
	if($text[0] == "setup"){
		addMe($csv, $Filename, $_POST['user_name']);
	}
	if($text[0] =="menu"){
		menu($snackcsv);
	}
	if($text[0] =="help"){
		echo("To set up your Travis account first use".'"/travis setup"'." (this adds your name to the tracking sheet) \n". '"/travis add [amount]"'. " will add the specified amount to your account \n". '"/travis subtract [amount]"'." will remove the specified amount from your account \n".'"/travis funds"'. " will tell you the amount left in your account \n".'"/travis menu" '. "Will tell you the items available for purchase and their prices\n".'"/travis buy [item]"'." will buy the item specified, use menu for the names of items\n");  
	}
//	echo($_POST['channel_name']);

	//$data = makePackage("meh");

	//sendPackage($data, $Wurl);
//}
//else{
	//echo($_POST['channel_name']);
//	die("command not allowed in this channel");
//}

//closing------------------------------------
echo("Have a purrrfect day!");


?>

