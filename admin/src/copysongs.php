<?php
    if( !defined('INDEX') ) {
        header('Location: ../index.php');
        die;
    }
    
    require_once( 'includes/gob_admin.php' );
    require_once( '../src/secrets.php' );
    require_once( '../src/query.php' );
    
    function copyMP3($db, $teamnumber, $name, $url){
        $sclink = "https://soundcloud.com/";
        $sc2mp3 = "https://soundcloud2mp3.com/";
  
        if(strpos($url,$sclink) !== false){
            $scdata = str_replace($sclink, "", $url);
            $mp3url = $sc2mp3  .  $scdata  .  "/download";
            $safename= substr($scdata, strpos($scdata,"/"));
        
            /* Only successful if extension=php_openssl.dll is enabled in php.ini, test with:
            $w = stream_get_wrappers();
            echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "\n";
            echo 'http wrapper: ', in_array('http', $w) ? 'yes':'no', "\n";
            echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "\n";
            echo 'wrappers: ', var_dump($w); */
        
            $mp3 = @file_get_contents($mp3url);
        
            //Handle potential failure of file_get_contents with cURL fallback
            if($mp3 === FALSE){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_URL, $mp3url);
                curl_setopt($ch, CURLOPT_REFERER, $mp3url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $mp3 = curl_exec($ch);
                curl_close($ch);
                
            }
        
        
        $filename = "team_"  .  $teamnumber  .  "_"  .  $safename  .  ".mp3";
        
        file_put_contents($filename,$mp3);
        
        }
    }
    
    $db = database_connect();
    
    $songs_to_copy = $db->query('SELECT * FROM songs WHERE approved="" ORDER BY id DESC');
    


    
?>
