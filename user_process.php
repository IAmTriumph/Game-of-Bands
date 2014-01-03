<?php session_start();

require_once('src/gob_user.php');
require_once('lib/reddit.php');
require_once('src/secrets.php');
require_once('src/query.php');

loggedin_check();

$reddit = new reddit($reddit_user, $reddit_password);

if( isset($_POST['leaveTeam']) ){
  leaveTeam();

} elseif ( isset($_POST['submitSongPage']) ) {
  goSubmitSongPage();

} elseif( isset($_POST['submitSong']) ){
  submitSong($reddit);

}

function leaveTeam(){
    $user = $_SESSION['GOB']['name'];
    $response = $reddit->sendMessage('/r/waitingforgobot', $user . ' Wants To Leave His Team', $user . ' wants to leave team 1.');
    
    redirect();

}

function goSubmitSongPage(){
    redirect('/user_submitsong');

}

function submitSong($reddit){
    $db = database_connect();

    $user = $_SESSION['GOB']['name'];
    $round = filter_input(INPUT_POST, 'round', FILTER_SANITIZE_NUMBER_INT);
    $teamnumber = filter_input(INPUT_POST, 'teamnumber', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'songname', FILTER_SANITIZE_URL);
    $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);
    $lyrics = filter_input(INPUT_POST, 'lyrics', FILTER_SANITIZE_SPECIAL_CHARS);
    $music = filter_input(INPUT_POST, 'music', FILTER_SANITIZE_SPECIAL_CHARS);
    $vocals = filter_input(INPUT_POST, 'vocals', FILTER_SANITIZE_SPECIAL_CHARS);
    $lyricsheet = filter_input(INPUT_POST, 'lyricsheet', FILTER_SANITIZE_SPECIAL_CHARS);

    $newSong = $db->prepare('INSERT INTO songs (name, url, music, lyrics, vocals, lyricsheet, round, teamnumber, submitby, approved) VALUES (:name, :url, :music, :lyrics, :vocals, :lyricsheet, :round, :teamnumber, :submitby, :approved)');
    $newSong->execute(array(':name' => $name,
                          ':url' => $url,
                          ':music' => $music,
                          ':lyrics' => $lyrics,
                          ':vocals' => $vocals,
                          ':lyricsheet' => $lyricsheet,
                          ':round' => $round,
                          ':teamnumber' => $teamnumber,
                          ':submitby' => $user,
                          ':approved' => false
    ));

    
    $response = $reddit->sendMessage('/r/gameofbands', 'Team ' . $teamnumber, $user . ' submitted the song ' . $name );
    redirect();

}

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


function redirect($page = 'index.php'){
    header('Location: '.$page);

}

?>
