<?php

#------------------------------------------
# Plugin for WDTV Live
# Spanish Radios available online
#
# @author Ricardo Ribalda
# @version 0.1
# @date 11/01/2011
#
#------------------------------------------


include ('funciones.php');

function parseM3U($file){
	$mp3list=array();

	$m3uFile=file($file);

	if((!preg_match("/#EXTM3U/i",$m3uFile[0]))&&(!preg_match("/http/i",$m3uFile[0])))
	 	return $mp3list;

	foreach($m3uFile as $line)
		if(preg_match('/^http/i',$line))
			$mp3list[] = trim($line);

	return $mp3list;
}

function parsePLS($file){
	$mp3list=array();

	$plsFile=file($file);

	if(!preg_match("/[playlist]/i",$plsFile[0]))
	 	return $mp3list;

	foreach($plsFile as $line){
		if(preg_match("/http:(.*)/",$line,$https))
			$mp3list[]=trim("http:".$https[1]);
	}

	return $mp3list;
}

function parseURL($url){
	$radios=array();

	if (preg_match("/m3u$/i",$url))
		$radios=parseM3U($url);
	else if (preg_match("/pls$/i",$url))
		$radios=parsePLS($url);
	else
		$radios[]=$url;

	return $radios;
}

function _pluginCreateStationList() {
	$queryData= array();
	$radios= array (
		array(
			"name" => "Rádio Nacional de España",
			"ico"  => "http://oi53.tinypic.com/2ly3cch.jpg",
			"url"  => "http://radio1.rtve.stream.flumotion.com/rtve/radio1.mp3.m3u",
			"use_proxy" => 0,
		),
		array(
			"name" => "Rádio 3",
			"ico"  => "http://oi51.tinypic.com/2iixu9j.jpg",
			"url"  => "http://radio3.rtve.stream.flumotion.com/rtve/radio3.mp3.m3u",
			"use_proxy" => 0,
		),
		array(
			"name" => "Rádio 5 - Todo Noticias",
			"ico"  => "http://oi52.tinypic.com/260twua.jpg",
			"url"  => "http://radio5.rtve.stream.flumotion.com/rtve/radio5.mp3.m3u",
			"use_proxy" => 0,
		),
		array(
			"name" => "Rádio Clásica",
			"ico"  => "http://oi55.tinypic.com/2d8lmp.jpg",
			"url"  => "http://radioclasica.rtve.stream.flumotion.com/rtve/radioclasica.mp3.m3u",
			"use_proxy" => 0,
		),
		array(
			"name" => "Rádio Exterior",
			"ico"  => "http://oi55.tinypic.com/23har9t.jpg",
			"url"  => "http://radioexterior.rtve.stream.flumotion.com/rtve/radioexterior.mp3.m3u",
			"use_proxy" => 0,
		),
		array(
			"name" => "Cadena Ser",
			"ico"  => "http://oi56.tinypic.com/2je7fch.jpg",
			"url"  => "http://194.169.201.177:8085/liveser.mp3",
			"use_proxy" => 1,
		),
		array(
			"name" => "M80",
			"ico"  => "http://oi52.tinypic.com/24v71p3.jpg",
			"url"  => "http://194.169.201.177:8085/liveM80.mp3",
			"use_proxy" => 1,
		),
		array(
			"name" => "40 Principales",
			"ico"  => "http://oi56.tinypic.com/287hh1y.jpg",
			"url"  => "http://194.169.201.177:8085/live3.mp3",
			"use_proxy" => 1,
		),
		array(
			"name" => "Cadena Dial",
			"ico"  => "http://oi55.tinypic.com/vikx1y.jpg",
			"url"  => "http://194.169.201.177:8085/liveDial.mp3",
			"use_proxy" => 1,
		),
		array(
			"name" => "Máxima FM",
			"ico"  => "http://oi56.tinypic.com/1z3njuv.jpg",
			"url"  => "http://194.169.201.177:8085/liveMaxima.mp3",
			"use_proxy" => 1,
		),
		array(
			"name" => "Radiolé",
			"ico"  => "http://oi55.tinypic.com/1s15yd.jpg",
			"url"  => "http://194.169.201.177:8085/liveRadiOle.mp3",
			"use_proxy" => 1,
		),
		array(
			"name" => "Cope",
			"ico"  => "http://oi52.tinypic.com/2prcsap.jpg",
			"url"  => "http://copefm.cope.stream.flumotion.com/cope/copefm.mp3.m3u",
			"use_proxy" => 0,
		),
		array(
			"name" => "Rock and Gol",
			"ico"  => "http://oi52.tinypic.com/nvpp8n.jpg",
			"url"  => "http://rockandgol.cope.stream.flumotion.com/cope/copefm.mp3.m3u",
			"use_proxy" => 0,
		),
		array(
			"name" => "Punto Radio",
			"ico"  => "http://oi55.tinypic.com/15hbh9l.jpg",
			"url"  => "http://provisioning.streamtheworld.com/pls/NATIONAL.pls",
			"use_proxy" => 0,
		),
		array(
			"name" => "Loca FM",
			"ico"  => "http://oi55.tinypic.com/2rh4scj.jpg",
			"url"  => "http://server2.20comunicacion.com:8024",
			"use_proxy" => 1,
		),
		array(
			"name" => "BBC World",
			"ico"  => "http://oi53.tinypic.com/2luvyn7.jpg",
			"url"  => "http://www.vpr.net/vpr_files/stream_playlists/vpr_bbc_mp3.pls",
			"use_proxy" => 1,
		),
	);

	foreach ($radios as $radio){
		$urls=parseURL($radio["url"]);

		if (sizeof($urls)>1){
			if ($radio["use_proxy"])
				$aux=array(
					"station_url"=>"proxy:".$radio["url"],
				);
			else
				$aux=array(
					"station_url"=>$radio["url"],
				);
			$retMediaItems[]=array(
				"id" => 'umsp://plugins/spanishradio?'.http_build_query($aux,'pluginvar_'),
				"dc:title" => $radio["name"],
				"upnp:album_art" => $radio["ico"],
				"upnp:class" => "object.container",
			);
		}
		else{
			if ($radio["use_proxy"])
				$res= "http://localhost/umsp/plugins/mp3-proxy.php?itemURL=".$urls[0];
			else
				$res= $urls[0];
			$retMediaItems[]=array(
				"id" => 'umsp://plugins/spanishradio2?'.urlencode($radio["name"]),
				"dc:title" => $radio["name"],
				"res" => $res,
				"upnp:album_art" => $radio["ico"],
				'upnp:class' => 'object.item.audioItem',
				'protocolInfo'  => 'http-get:*:audio/mpeg:*',
			);
		}
	}

	return $retMediaItems;

}

function _pluginCreateStationItems($url){
	$retMediaItems=array();
	$proxy=0;

	if(preg_match("/^proxy:(.*)/",$url,$res)){
		$use_proxy=1;
		$url=$res[1];
	}
	else
		$use_proxy=0;

	$radios=parseURL($url);

	$n=0;
	foreach ($radios as $radio){
		$n++;
		if ($use_proxy)
			$res= "http://localhost/umsp/plugins/mp3-proxy.php?itemURL=".$radio;
		else
			$res= $radio;
		$retMediaItems[]=array(
			"id" => 'umsp://plugins/spanishradio?'.urlencode($res),
			"dc:title" => $res,
			"res" => $res,
			'upnp:class' => 'object.item.audioItem',
			'protocolInfo'  => 'http-get:*:audio/mpeg:*',
		);
	}
	return $retMediaItems;
}

function _pluginMain($prmQuery){
	$queryData=array();
	parse_str($prmQuery,$queryData);

	if (isset($queryData['station_url'])){
		return _pluginCreateStationItems($queryData['station_url']);
	}
	return _pluginCreateStationList();
}

//print_r(_pluginMain(""));

?>
