<?php

#------------------------------------------
# Plugin for WDTV Live
# Recordings from Defcon events. Profit
#
# @author Ricardo Ribalda
# @version 0.1
# @date 13/01/2011
#
#------------------------------------------


include ('funciones.php');

function _pluginCreateEventList() {
	$queryData= array();
	$events= array (
		array(
			"name" => "DEF CON 1",
			"ico"  => "http://defcon.org/images/defcon-1/defcon-1-logo-1.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-1-archive.html",
		),
		array(
			"name" => "DEF CON 2",
			"ico"  => "http://defcon.org/images/defcon-2/defcon-2-logo-1.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-2-archive.html",
		),
		array(
			"name" => "DEF CON 3",
			"ico"  => "http://defcon.org/images/defcon-3/defcon-3-logo-1.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-3-archive.html",
		),
		array(
			"name" => "DEF CON 4",
			"ico"  => "http://defcon.org/images/defcon-4/defcon-4-logo-1.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-4-archive.html",
		),
		array(
			"name" => "DEF CON 5",
			"ico"  => "http://defcon.org/images/defcon-5/dc-5-31.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-5-archive.html",
		),
		array(
			"name" => "DEF CON 6",
			"ico"  => "http://defcon.org/images/defcon-6/dc6-logob.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-6-archive.html",
		),
		array(
			"name" => "DEF CON 7",
			"ico"  => "http://defcon.org/images/defcon-7/dc7logoblack-header.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-7-archive.html",
		),
		array(
			"name" => "DEF CON 8",
			"ico"  => "http://defcon.org/images/defcon-8/dc-8-pill.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-8-archive.html",
		),
		array(
			"name" => "DEF CON 9",
			"ico"  => "http://defcon.org/images/defcon-9/dc9-archive-logo.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-9-archive.html",
		),
		array(
			"name" => "DEF CON 10",
			"ico"  => "http://defcon.org/images/defcon-10/dc-0A-badge-inv.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-10-archive.html",
		),
		array(
			"name" => "DEF CON 11",
			"ico"  => "http://defcon.org/images/defcon-11/dc-11-badge/dc-11-badge.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-11-archive.html",
		),
		array(
			"name" => "DEF CON 12",
			"ico"  => "http://defcon.org/images/defcon-12/dc12-badge2.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-12-archive.html",
		),
		array(
			"name" => "DEF CON 13",
			"ico"  => "http://defcon.org/images/defcon-13/dc13-badges/dc-13-badge-cu.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-13-archive.html",
		),
		array(
			"name" => "DEF CON 14",
			"ico"  => "http://defcon.org/images/defcon-14/dc-14-sm.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-14-archive.html",
		),
		array(
			"name" => "DEF CON 15",
			"ico"  => "http://defcon.org/images/defcon-15/dc-15-archive.jpg",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-15-archive.html",
		),
		array(
			"name" => "DEF CON 16",
			"ico"  => "http://defcon.org/images/defcon-16/dc-16-logo.png",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-16-archive.html",
		),
		array(
			"name" => "DEF CON 17",
			"ico"  => "http://defcon.org/images/defcon-17/dc-17-logo.png",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-17-archive.html",
		),
		array(
			"name" => "DEF CON 18",
			"ico"  => "http://defcon.org/images/defcon-18/dc-18-logo_smsq.png",
			"url"  => "http://defcon.org/html/links/dc-archives/dc-18-archive.html",
		),
	);

	foreach ($events as $event){

		$aux=array(
			"station_url"=>$event["url"],
		);
		$retMediaItems[]=array(
			"id" => 'umsp://plugins/defcon?'.http_build_query($aux,'pluginvar_'),
			"dc:title" => $event["name"],
			"upnp:album_art" => $event["ico"],
			"upnp:class" => "object.container",
		);
	}

	return $retMediaItems;

}

function _pluginCreateEventItems($url){
	$indexfile=file($url);

	//Find links
	$links=array();
	foreach ($indexfile as $line){
		if (preg_match_all("/href=\"([^\"]+)\"/i",$line,$hrefs)){
			foreach ($hrefs[1] as $href)
				$links[]=$href;
		}
	}
	$retMediaItems=array();
	foreach ($links as $link){
		//Select only media files
		if (!((preg_match("/mp3$/i",$link))||
			(preg_match("/m4b$/i",$link))||
			(preg_match("/rm$/i",$link))||
			(preg_match("/mp4$/i",$link))||
			(preg_match("/m4v$/i",$link))))
			continue;

		//hhtps->http
		$link=preg_replace("/^https/","http",$link);

		//Create title
		preg_match("/\/([^\/]*)$/i",$link,$ma);
		$file=$ma[1];
		preg_match("/(.*)\....?$/i",$file,$ma);
		$file=$ma[1];

		$album="DefCon";
		$author="Electronic Frontier Fundation";
		$title=ucfirst(strtr($file,"_-","  "));

		if (preg_match("/^DEF *CON ([^ ]+) Hacking Conference Presentation By -* *([^-]*) - (.*)$/i",$file,$ma)){
			$album="DefCon".$ma[1];
			$author=ucfirst(strtr($ma[2],"_-","  "));
			$title=ucfirst(strtr($ma[3],"_-","  "));
		}
		else if (preg_match("/^Defcon([^-]+)-([^-]+)-(.*)$/i",$file,$ma)){
			$album="DefCon".$ma[1];
			$author=ucfirst(strtr($ma[2],"_-","  "));
			$title=ucfirst(strtr($ma[3],"_-","  "));
		}
		else if (preg_match("/[^-]*-Defcon_([^-]+)-v[^-]*-([^-]+)-(.*)$/i",$file,$ma)){
			$album="DefCon".$ma[1];
			$author=ucfirst(strtr($ma[2],"_-","  "));
			$title=ucfirst(strtr($ma[3],"_-","  "));
		}
		else if (preg_match("/[^-]*-Defcon_([^-]+)-[^-]+-([^-]+)-(.*)$/i",$file,$ma)){
			$album="DefCon".$ma[1];
			$author=ucfirst(strtr($ma[2],"_-","  "));
			$title=ucfirst(strtr($ma[3],"_-","  "));
		}
		else if (preg_match("/([^_-]*)[_-]Defcon[^-]+-([^-]+)-(.*)$/i",$file,$ma)){
			$album=$ma[1]." DefCon";
			$author=ucfirst(strtr($ma[2],"_-","  "));
			$title=ucfirst(strtr($ma[3],"_-","  "));
		}
		else if (preg_match("/([^ ]*) Defcon V[^ ]+ - ([^-]+) - (.*)$/i",$file,$ma)){
			$album=$ma[1]." DefCon";
			$author=ucfirst(strtr($ma[2],"_-","  "));
			$title=ucfirst(strtr($ma[3],"_-","  "));
		}
		else if (preg_match("/dc-([^-]*)-(.*)$/i",$file,$ma)){
			$album="DefCon".$ma[1];
			$title=ucfirst(strtr($ma[2],"_-","  "));
		}
		else if (preg_match("/([^_]*)_Defcon_v[^-]*-([^-]+)-(.*)$/i",$file,$ma)){
			$album=$ma[1]." DefCon";
			$author=ucfirst(strtr($ma[2],"_-","  "));
			$title=ucfirst(strtr($ma[3],"_-","  "));
		}

		$retMediaItem=array();
		if ((preg_match("/mp4$/i",$link))||(preg_match("/m4v$/i",$link))){
			$retMediaItem["res"]=$link;
			$retMediaItem["protocolInfo"]='http-get:*:video/mpeg:*';
			$retMediaItem["upnp:class"]='object.item.videoItem';
		}
		if ((preg_match("/mp3$/i",$link))||(preg_match("/m4b$/i",$link))||(preg_match("/rm$/i",$link))){
			$retMediaItem["res"]=$link;
			//$retMediaItem["res"]="http://localhost/umsp/plugins/mp3-proxy.php?itemURL=".$link;
			$retMediaItem["protocolInfo"]='http-get:*:audio/mpeg:*';
			$retMediaItem["upnp:class"]='object.item.audioItem';
		}

		$retMediaItem["id"] = 'umsp://plugins/defcon?'.urlencode($link);
		$retMediaItem["dc:title"]=$title;
		//$retMediaItem["dc:title"]=$link;
		$retMediaItem["upnp:author"]= $author;
		$retMediaItem["upnp:album"]= $album;

		$retMediaItems[]=$retMediaItem;
	}

	return $retMediaItems;
}

function _pluginMain($prmQuery){
	$queryData=array();
	parse_str($prmQuery,$queryData);

	if (isset($queryData['station_url'])){
		return _pluginCreateEventItems($queryData['station_url']);
	}
	return _pluginCreateEventList();
}

//print_r(_pluginMain(""));
//print_r(_pluginCreateEventItems("http://defcon.org/html/links/dc-archives/dc-1-archive.html"));

?>
