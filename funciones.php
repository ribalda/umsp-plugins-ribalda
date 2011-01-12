<?php
#------------------------------------------
# Biblioteca de funciones para los plugins para WDTV Live
#
# @author kito0791
# @version 0.2
# @date 15/09/2010
#
# Funciones iniciales:
#	- decrypt($str, $key1, $key2) (function from megavideo.class.php by luruke)
#	- _obtenerUrlMegavideoPremium($codigo,$cookie)
#	- _obtenerUrlMegavideo($codigo)
#
# @author kito0791 	- Nuevas funciones para la inclusión de favoritos directamente desde el cacharro.
#	- _pluginFavorito($url)
#	- _favoritos($url,$website,$caratula,$serie)
# @date 15/11/2010
#------------------------------------------


# decrypt function from megavideo.class.php by luruke
function decrypt($str, $key1, $key2) {
  $reg1 = array();

  for($reg3=0; $reg3<strlen($str); $reg3++) {
    $reg0 = $str[$reg3];
    switch($reg0) {
      case '0': $reg1[] = '0000'; break;
      case '1': $reg1[] = '0001'; break;
      case '2': $reg1[] = '0010'; break;
      case '3': $reg1[] = '0011'; break;
      case '4': $reg1[] = '0100'; break;
      case '5': $reg1[] = '0101'; break;
      case '6': $reg1[] = '0110'; break;
      case '7': $reg1[] = '0111'; break;
      case '8': $reg1[] = '1000'; break;
      case '9': $reg1[] = '1001'; break;
      case 'a': $reg1[] = '1010'; break;
      case 'b': $reg1[] = '1011'; break;
      case 'c': $reg1[] = '1100'; break;
      case 'd': $reg1[] = '1101'; break;
      case 'e': $reg1[] = '1110'; break;
      case 'f': $reg1[] = '1111'; break;
    }
  }

  $reg1 = join($reg1);
  $reg6 = array();

  for($reg3=0; $reg3<384; $reg3++) {
    $key1 = ($key1 * 11 + 77213) % 81371;
    $key2 = ($key2 * 17 + 92717) % 192811;
    $reg6[] = ($key1 + $key2) % 128;
  }

  for($reg3=256; $reg3>=0; $reg3--) {
    $reg5 = $reg6[$reg3];
    $reg4 = $reg3 % 128;
    $reg8 = $reg1[$reg5];
    $reg1[$reg5] = $reg1[$reg4];
    $reg1[$reg4] = $reg8;
  }

  for($reg3=0; $reg3<128; $reg3++) {
    $reg1[$reg3] = $reg1[$reg3] ^ ($reg6[$reg3+256] & 1);
  }

  $reg12 = $reg1;
  $reg7 = array();

  for($reg3=0; $reg3<strlen($reg12); $reg3+=4) {
    $reg9 = substr($reg12, $reg3, 4);
    $reg7[] = $reg9;
  }

  $reg2 = array();

  for($reg3=0; $reg3<count($reg7); $reg3++) {
    $reg0 = $reg7[$reg3];
    switch($reg0) {
      case '0000': $reg2[] = '0'; break;
      case '0001': $reg2[] = '1'; break;
      case '0010': $reg2[] = '2'; break;
      case '0011': $reg2[] = '3'; break;
      case '0100': $reg2[] = '4'; break;
      case '0101': $reg2[] = '5'; break;
      case '0110': $reg2[] = '6'; break;
      case '0111': $reg2[] = '7'; break;
      case '1000': $reg2[] = '8'; break;
      case '1001': $reg2[] = '9'; break;
      case '1010': $reg2[] = 'a'; break;
      case '1011': $reg2[] = 'b'; break;
      case '1100': $reg2[] = 'c'; break;
      case '1101': $reg2[] = 'd'; break;
      case '1110': $reg2[] = 'e'; break;
      case '1111': $reg2[] = 'f'; break;
    }
  }

  return join($reg2);

}

function _obtenerUrlMegavideoPremium($codigo,$cookie)
{

  $xmlMegavideo = file_get_contents("http://www.megavideo.com/xml/player_login.php?u=".$cookie."&v=".$codigo);
  preg_match('/ downloadurl="(.*?)"/',$xmlMegavideo,$url);
  $myurl = rawurldecode($url[1]);

  return $myurl;
}

function _obtenerUrlMegavideo($codigo)
{
  $xmlMegavideo = file_get_contents("http://www.megavideo.com/xml/videolink.php?v=".$codigo."&id=".time());
  preg_match('/ s="(.*?)"/',$xmlMegavideo,$server);
  preg_match('/ k1="(.*?)"/',$xmlMegavideo,$key1);
  preg_match('/ k2="(.*?)"/',$xmlMegavideo,$key2);
  preg_match('/ un="(.*?)"/',$xmlMegavideo,$hash);
  $myurl = "http://www" . $server[1] . ".megavideo.com/files/" . decrypt($hash[1],$key1[1],$key2[1]) . "/video.flv";

  return $myurl;
}

function _pluginFavorito($url) {

	$datos = explode("_|_", $url);
	$path = '/conf/favoritos.xml';
	if (substr(sprintf('%o', fileperms($path)), -4)=='0644') shell_exec('sudo chmod a+w '.$path);
	$items = new SimpleXMLElement($path, null, true);

	$favorito= $items->addChild('favorito');
	$favorito->addAttribute('nombre', utf8_encode($datos[3]));
	$favorito->addAttribute('url', $datos[0]);
	$favorito->addAttribute('imagen', $datos[2]);
	$favorito->addAttribute('website', $datos[1]);
	$favorito->addAttribute('orden', ($items->count()));
	$items->asXML($path);

	//($datos[1]=="tumejortv") ? $tipourl = 'ficha_url': $tipourl = 'movie_url';
	$tipourl = 'movie_url';

	$data = array($tipourl => $datos[0]);
	$dataString = http_build_query($data, 'pluginvar_');
	$retMediaItems[] = array (
		'id' => 'umsp://plugins/'.$datos[1].'?'.$dataString,
		'dc:title' => html_entity_decode('Se ha incluido en favoritos (Volver)',0,"UTF-8"),
		'upnp:class' => 'object.container',
		);
	return $retMediaItems;
}

function _favoritos($url,$website,$caratula,$serie)
{
	$data = array('favoritos' => $url.'_|_'.$website.'_|_'.$caratula.'_|_'.$serie);
	$dataString = http_build_query($data, 'pluginvar_');
	$item = array (
		'id' => 'umsp://plugins/'.$website.'?'.$dataString,
		'dc:title' => html_entity_decode('Incluir en favoritos',0,"UTF-8"),
		'upnp:class' => 'object.container',
		'upnp:album_art'=> 'http://i52.tinypic.com/x0xm2s.jpg',
		);
	return $item;
}

?>
