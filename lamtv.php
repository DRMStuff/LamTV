<?php
/*
get.php?contenido=live_tv
get.php?contenido=series
get.php?contenido=peliculas
get.php?contenido=peliculas_xxx
*/

error_reporting(0);
#session_start();

ini_set('max_execution_time', 50000);
header('Content-Type: text/plain');
$user_agent= 'U7PtaMPgBvtsl_iH';

if (isset($_GET['ts'])) {
    header('Content-Type: video/MP2T');
    echo cURL(rtrim($_GET['ts']));
    exit();
    }
    
$server      = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";
$path_logo   = str_replace('lamtv_get.php', '', $server);
$path_poster = 'http://158.69.24.110/posters/';


if ($_GET['contenido'] == 'peliculas') {
    echo '#EXTM3U'.PHP_EOL;
    $json_categorias = json_decode(cURL('http://158.69.24.110:8085/API_Android_Segura/contenido_peliculas/categorias_peliculas_object.php?lang=es_ES'), true);
    for ($i = 0; $i < count($json_categorias['Categories']); $i++) {
        $grupo_categoria = $json_categorias['Categories'][$i]['name'];
        $json_peliculas  = json_decode(cURL('http://158.69.24.110:8085/API_Android_Segura/contenido_peliculas/contenido_peliculas_fast.php?cat=Por_Categoria&cve_cat='.$json_categorias['Categories'][$i]['cve']), true);
        for ($j = 0; $j < count($json_peliculas['Videos']); $j++) {
            $json_pelicula = json_decode(cURL('http://158.69.24.110:8085//API_Android_Segura/contenido_peliculas/detalle_peliculas.php?cve_contenido='.$json_peliculas['Videos'][$j]['cve']), true);
            echo '#EXTINF:0 group-title="'.$grupo_categoria.'" tvg-logo="'.$path_poster.$json_peliculas['Videos'][$j]['poster'].'",'.strtoupper($json_peliculas['Videos'][$j]['titulo']).PHP_EOL.$json_pelicula['url_video'].PHP_EOL;
        }
    }
}

if ($_GET['contenido'] == 'peliculas_xxx') {
    echo '#EXTM3U'.PHP_EOL;
    $json_categorias = json_decode(cURL('http://158.69.24.110:8085/API_Android_Segura/contenido_livetv/categorias_livetv.php?tv_region=5'), true);
    for ($i = 0; $i < count($json_categorias['CatLiveTV']); $i++) {
        $grupo_categoria = $json_categorias['CatLiveTV'][$i]['categoria'];
        $json_peliculas  = json_decode(cURL('http://158.69.24.110:8085//API_Android_Segura/contenido_adultos/videos_adultos.php?cve_cat='.$json_categorias['CatLiveTV'][$i]['cve']), true);
        for ($j = 0; $j < count($json_peliculas['Videos']); $j++) {
            $json_pelicula = json_decode(cURL('http://158.69.24.110:8085//API_Android_Segura/contenido_peliculas/detalle_peliculas.php?cve_contenido='.$json_peliculas['Videos'][$j]['cve']), true);
            echo '#EXTINF:0 group-title="'.$grupo_categoria.'" tvg-logo="'.$path_poster.$json_peliculas['Videos'][$j]['poster'].'",'.strtoupper($json_peliculas['Videos'][$j]['titulo']).PHP_EOL.$json_pelicula['url_video'].PHP_EOL;
        }
    }
}

if ($_GET['contenido'] == 'series') {
    echo '#EXTM3U'.PHP_EOL;
    $json_categorias = json_decode(cURL("http://158.69.24.110:8085/API_Android_Segura/contenido_series/categorias_series_object.php"), true);
    for ($i = 0; $i < count($json_categorias['Categories']); $i++) {
        $grupo_categoria = $json_categorias['Categories'][$i]['name'];
        $json_categoria  = json_decode(cURL("http://158.69.24.110:8085/API_Android_Segura/contenido_series/contenido_series.php?cat=Por_Categoria&cve_cat=".$json_categorias['Categories'][$i]['cve']), true);
        for ($j = 0; $j < count($json_categoria['Videos']); $j++) {
            $titulo_serie = $json_categoria['Videos'][$j]['titulo'];
            $json_serie   = json_decode(cURL('http://158.69.24.110:8085//API_Android_Segura/contenido_series/temporadas_serie.php?cve='.$json_categoria['Videos'][$j]['cve']), true);
            for ($h = 0; $h < count($json_serie['Temporadas']); $h++) {
                $numero_temporada = $h + 1;
                $json_temporada   = json_decode(cURL('http://158.69.24.110:8085//API_Android_Segura/contenido_series/capitulos.php?cve='.$json_categoria['Videos'][$j]['cve'].'&temporada='.$numero_temporada), true);
                for ($l = 0; $l < count($json_temporada['Capitulos']); $l++) {
                    echo '#EXTINF:0 group-title="'.$grupo_categoria.'" tvg-logo="'.$path_poster.$json_temporada['Capitulos'][$l]['Image'].'",'.strtoupper($titulo_serie.' Temporada '.$numero_temporada.' '.$json_temporada['Capitulos'][$l]['nombre']).PHP_EOL.$json_temporada['Capitulos'][$l]['urlVideo'].PHP_EOL;

                }
            }
        }
    }
}

if ($_GET['contenido'] == 'live_tv') {
    echo '#EXTM3U'.PHP_EOL;
    $array_categorias = array("6", "8", "10", "11");
    for ($h = 0; $h < count($array_categorias); $h++) {
        $json_categorias = json_decode(cURL('http://158.69.24.110:8085/API_Android_Segura/contenido_livetv/categorias_livetv.php?tv_region='.$array_categorias[$h]), true);
        for ($i = 0; $i < count($json_categorias['CatLiveTV']); $i++) {
            $grupo_categoria = $json_categorias['CatLiveTV'][$i]['categoria'];
            $json_categoria  = json_decode(cURL('http://158.69.24.110:8085/API_Android_Segura/contenido_livetv/contenido_livetv.php?cat='.$json_categorias['CatLiveTV'][$i]['cve']), true);
            for ($j = 0; $j < count($json_categoria['LiveTV']); $j++) {
                echo '#EXTINF:-1 user-agent="'.$user_agent.'" group-title="'.$grupo_categoria.'" tvg-logo="'.$path_poster.$json_categoria['LiveTV'][$j]['poster'].'",'.strtoupper($json_categoria['LiveTV'][$j]['nombre']).PHP_EOL.$server.'?get=live&url='.base64_encode( $json_categoria['LiveTV'][$j]['url_video'] ).'&type=playlist.m3u8'.PHP_EOL.PHP_EOL;
            }
        }
    }
    $json = json_decode(cURL('http://158.69.24.110:8085/API_Android_Segura/contenido_kids/kids_tv.php'), true);
    for ($i = 0; $i < count($json['LiveTV']); $i++) {
        echo '#EXTINF:-1 user-agent="'.$user_agent.'" group-title="KIDS" tvg-logo="'.$path_poster.$json['LiveTV'][$i]['poster'].'",'.strtoupper($json['LiveTV'][$i]['nombre']).PHP_EOL.$server.'?get=live&url='.base64_encode( $json['LiveTV'][$i]['url_video'] ).'&type=playlist.m3u8'.PHP_EOL.PHP_EOL;
    }
    $json = json_decode(cURL('http://158.69.24.110:8085/API_Android_Segura/contenido_adultos/adultos_live.php'), true);
    for ($i = 0; $i < count($json); $i++) {
        echo '#EXTINF:-1 user-agent="'.$user_agent.'" group-title="ADULTOS-XXX" tvg-logo="'.$json[$i]['poster'].'",'.strtoupper($json[$i]['titulo']).PHP_EOL.$server.'?get=live&url='.base64_encode( $json[$i]['liga'] ).'&type=playlist.m3u8'.PHP_EOL.PHP_EOL;
    }

}
/*
if($_GET['get'] == 'live'){
	if (is_null($_SESSION[$_GET['url']])) {
		$id = base64_decode($_GET['url']);
		$path_m3u8 = str_replace(end(explode('/' , $id)) , '' , $id);
		$path = explode(PHP_EOL , cURL($id));
		$_SESSION[$_GET['url']] = $path_m3u8.$path[3];
		}
	$path_m3u8 = str_replace(end(explode('/' , $_SESSION[$_GET['url']] )) , '' , $_SESSION[$_GET['url']] );
	echo str_replace('l_' , $server.'?ts='.$path_m3u8.'l_' , cURL($_SESSION[$_GET['url']]));
	}

if ($_GET['get'] == 'live') {
	#header ('Location: '.base64_decode($_GET['url'])); exit();
	if (is_null($_SESSION[$_GET['url']])) {
		$hls = cURL2(base64_decode($_GET['url']));
		$path = explode(chr(10), $hls);
		$_SESSION[$_GET['url']] = str_replace('Location: ',"",$path[6]);
		}
  $match = explode ('/' , $_SESSION[$_GET['url']] );
  echo str_replace($match[0], $server.'?ts='.$match[0],str_replace("/hlsr", $match[0].'//'.$match[2].'/hlsr', cURL( $_SESSION[$_GET['url']] )));
}
*/
if ($_GET['get'] == 'live') {
	$m3u8 = base64_decode($_GET['url']);
	$path = explode("live" , $m3u8);
	$nimble_m3u8 = cURL($m3u8);
	$path_chunk = explode(PHP_EOL , $nimble_m3u8);
	$chunk = explode("?" , $path_chunk[3]);
	if ($chunk[0] === "chunks.m3u8"){
		header ("Location:". $server."?get=live_nimble&url=".base64_encode(str_replace("playlist.m3u8" , "" , $m3u8).$path_chunk[3])."&type=.m3u8");
		}
	$content = str_replace("/hls" , $server."?ts=".$path[0]."hls" , cURL($m3u8));
	echo $content;
	}
	
if ($_GET['get'] == 'live_nimble') {
	$id_nimble = base64_decode($_GET['url']);
	$path_nimble = explode("/" , $id_nimble);
	$m3u8_path = str_replace(end($path_nimble) , "" , $id_nimble );
	echo str_replace("l_" , $server."?ts=".$m3u8_path."l_" , cURL($id_nimble));
	}


function cURL($url){
	$userAgent = 'U7PtaMPgBvtsl_iH';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$contenido = curl_exec($ch);
	curl_close($ch);
	return $contenido;
}

function cURL2($url){
    $agent='U7PtaMPgBvtsl_iH';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $page = curl_exec($ch);
    curl_close($ch);

    if($page){
        return $page;
    }
    return 'Forbidden';
 }

