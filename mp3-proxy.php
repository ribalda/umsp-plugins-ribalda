<?php
//Based on dreambox-proxy

if ( $_SERVER[''] == 'HEAD' ) {
	   header('Content-Type: audio/mpeg');
	      exit;
}

/*Get URL*/
$rawURL = $_GET['itemURL'];
$parsedURL = parse_url($rawURL);
$itemHost = $parsedURL['host'];
if (isset($parsedURL['port']))
	$itemPort = $parsedURL['port'];
else
	$itemPort = 80;
$itemPath = $parsedURL['path'];
$itemQuery = $parsedURL['query'];

$fp = fsockopen($itemHost, $itemPort, $errno, $errstr);
if (!$fp) {
	echo "$errstr ($errno)<br />\n";
	die();
}
# Create the HTTP GET request
if ((isset($itemQuery))&&($itemQuery!=""))
	$itemQuery="?$itemQuery";
else
	$itemQuery="";

if ((!isset($itemPath))||($itemPath)=="")
	$itemPath="/";

$out  = "GET $itemPath$itemQuery HTTP/1.0\r\n";
$out .= "User-Agent: Wget/1.12\r\n";
$out .= "Accept: */*\r\n";
$out .= "Host: $itemHost:$itemPort\r\n";
$out .= "Connection: Keep-Alive\r\n";
$out .= "\r\n";

fwrite($fp, $out);


header("Content-Type: audio/mpeg");

# Ignore the original headers

$headerpassed = false;
while ($headerpassed == false) {
	$line = fgets($fp);
	if ( $line == "\r\n" ) {
		$headerpassed = true;
	}
}

set_time_limit(0);
fpassthru($fp);
set_time_limit(30);

fclose($fp);
?>
