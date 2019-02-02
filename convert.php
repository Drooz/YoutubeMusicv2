<?php
require_once __DIR__ . '/vendor/autoload.php';

use YoutubeDl\YoutubeDl;

define("DOWNLOAD_FOLDER", dirname(__FILE__)."/download/"); //Be sure the chmod the download folder
define("DOWNLOAD_FOLDER_PUBLIC", "https://howtekno.com/music/youtube-dl-mp3-php-master/youtube-to-mp3/download/");

header("Content-Type: application/json");

if(isset($_GET["youtubelink"]) && !empty($_GET["youtubelink"]))
{
	$youtubelink = $_GET["youtubelink"];

	parse_str(parse_url($youtubelink, PHP_URL_QUERY), $queryvars);

	if(!array_key_exists("v", $queryvars))
	{
		die(json_encode(array("error" => true, "message" => "No video specified")));
	}

	$id = $queryvars["v"];
	$file = DOWNLOAD_FOLDER.$id.".mp3";

	$exists = file_exists($file);
	if($exists)
	{
		$options = array(
			'skip-download' => true
		);
	}
	else
	{
		$options = array(
			'extract-audio' => true,
			'audio-format' => 'mp3',
			'audio-quality' => 0, 
			'output' => '%(title)s..%(id)s.%(ext)s',
			//'ffmpeg-location' => '/usr/local/bin/ffmpeg'
		);
	}
	$dl = new YoutubeDl($options);
	$dl->setBinPath('/usr/local/bin/youtube-dl');
	$dl->setDownloadPath(DOWNLOAD_FOLDER);

	try 
	{
		$video = $dl->download($youtubelink);

		if($exists)
			$file = DOWNLOAD_FOLDER_PUBLIC.$id.".mp3";
		else
			$file = DOWNLOAD_FOLDER_PUBLIC.$video->getFilename();

		echo json_encode(array("error" => false, "title" => $video->getTitle(), "duration" => $video->getDuration(), "file" => $file));
	}
	catch (Exception $e)
	{
		echo json_encode(array("error" => true, "message" => $e->getMessage()));
	}
}
else if(isset($_GET["delete"]) && !empty($_GET["delete"]))
{
	$id = $_GET["delete"];

	if(unlink(DOWNLOAD_FOLDER.$id.".mp3"))
		echo json_encode(array("error" => false, "message" => "File removed"));
	else
		echo json_encode(array("error" => true, "message" => "File not found"));
}
else
	echo json_encode(array("error" => true, "message" => "Invalid request"));


/* Redirect browser */
header("Location: https://howtekno.com//lib/admin/t.php?catalogs[]=7");
 
/* Make sure that code below does not get executed when we redirect. */
exit;



?>

<html>
<head>
<title>A web page that points a browser to a different page after 2 seconds</title>
<meta http-equiv="refresh" content="4; URL=https://howtekno.com//lib/admin/t.php?catalogs[]=7">
<meta name="keywords" content="automatic redirection">
</head>
<body>
If your browser doesn't automatically go there within a few seconds, 
you may want to go to 
<a href="https://howtekno.com//lib/admin/t.php?catalogs[]=7">the destination</a> 
manually.
</body>
</html>
