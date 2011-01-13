<?php

#------------------------------------------
# Plugin for WDTV Live
# Recordings from CCC events. Profit
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
			"name" => "22C3",
			"ico"  => "http://oi52.tinypic.com/23lnlzt.jpg",
			"url"  => "http://mirror.fem-net.de/CCC/22C3/video/",
		),
		array(
			"name" => "23C3",
			"ico"  => "http://oi54.tinypic.com/2hgftdj.jpg",
			"url"  => "http://mirror.fem-net.de/CCC/23C3/video/",
		),
		array(
			"name" => "24C3",
			"ico"  => "http://oi53.tinypic.com/ieo7xz.jpg",
			"url"  => "http://mirror.fem-net.de/CCC/24C3/mp4/",
		),
		array(
			"name" => "25C3",
			"ico"  => "http://oi56.tinypic.com/aw9jet.jpg",
			"url"  => "http://mirror.fem-net.de/CCC/25C3/video_h264_720x576/",
		),
		array(
			"name" => "26C3",
			"ico"  => "http://oi52.tinypic.com/29oljxx.jpg",
			"url"  => "http://mirror.fem-net.de/CCC/26C3/mp4/",
		),
		array(
			"name" => "27C3",
			"ico"  => "http://oi51.tinypic.com/211m1w2.jpg",
			"url"  => "http://mirror.fem-net.de/CCC/27C3/mp4-h264-HQ/",
		),
		array(
			"name" => "CCCamp07",
			"ico"  => "http://oi53.tinypic.com/zjcew7.jpg",
			"url"  => "http://mirror.fem-net.de/CCC/CCCamp07/video/m4v/",
		),
		array(
			"name" => "SIGINT09",
			"ico"  => "http://oi55.tinypic.com/2n6yfyb.jpg",
			"url"  => "http://mirror.fem-net.de/CCC/sigint09/video/",
		),
		array(
			"name" => "LoungeMusic-27C3",
			"ico"  => "http://oi51.tinypic.com/2mzjswy.jpg",
			"url"  => "http://breitband.ccc.de/27c3/lounge/mp3/",
		),
	);

	foreach ($events as $event){

		$aux=array(
			"station_url"=>$event["url"],
		);
		$retMediaItems[]=array(
			"id" => 'umsp://plugins/ccc?'.http_build_query($aux,'pluginvar_'),
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
			(preg_match("/mp4$/i",$link))||
			(preg_match("/m4v$/i",$link))))
				continue;

		$title=$link;
		$ico="http://oi56.tinypic.com/245bx4m.jpg";
		$album="CCC Event";

		//Parse filename if possible
		if (preg_match("/([^_-]*)[_-]([^_-]*)[-_]([de][en])[-_](.*)....$/i",$link,$ma)){
			$album=$ma[1];
			if ($ma[3]=="de")
				$ico="http://oi56.tinypic.com/14j55x1.jpg";
			else
				$ico="http://oi54.tinypic.com/dpzpc2.jpg";
			$title=ucfirst(strtr($ma[4],"_-","  "));
			if (preg_match("/(.*) COMPATIBLE$/",$title,$ma))
				$title=$ma[1];
		}
		else if (preg_match("/([^_-]*)[_-]([de][en])[-_]([^_-]*)[-_](.*)....$/i",$link,$ma)){
			$album=$ma[1];
			if ($ma[2]=="de")
				$ico="http://oi56.tinypic.com/14j55x1.jpg";
			else
				$ico="http://oi54.tinypic.com/dpzpc2.jpg";
			$title=ucfirst(strtr($ma[4],"_-","  "));
		}
		else if (preg_match("/^27c3_Lounge_(.*)....$/i",$link,$ma)){
			$title=ucfirst(strtr($ma[1],"_-","  "));
		}

		$retMediaItem=array();
		$retMediaItem["id"] = 'umsp://plugins/ccc?'.urlencode($link);
		$retMediaItem["dc:title"]=$title;
		$retMediaItem["res"]=$url.$link;
		$retMediaItem["upnp:album_art"]=$ico;
		$retMediaItem["upnp:author"]= "Chaos Computer Club";
		$retMediaItem["upnp:album"]= $album;

		if ((preg_match("/mp4$/i",$link))||(preg_match("/m4v$/i",$link))){
			$retMediaItem["protocolInfo"]='http-get:*:video/mpeg:*';
			$retMediaItem["upnp:class"]='object.item.videoItem';
		}
		if (preg_match("/mp3$/i",$link)){
			$retMediaItem["res"]="http://localhost/umsp/plugins/mp3-proxy.php?itemURL=".$url.$link;
			$retMediaItem["protocolInfo"]='http-get:*:audio/mpeg:*';
			$retMediaItem["upnp:class"]='object.item.audioItem';
		}
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
//print_r(_pluginCreateEventItems("http://mirror.fem-net.de/CCC/23C3/video/"));

?>
