<?php
    function getVideoID($url) {
        $url_components = parse_url($url);
        parse_str($url_components['query'], $params);
        return $params['v'];
    }

    function getYoutubeDLCMD() {
        $proxy_cmd="";
        if (!empty($GLOBALS['proxy'])) $proxy_cmd = "--proxy ".$GLOBALS['proxy'];
        return 'youtube-dl '.$proxy_cmd.' --ffmpeg-location /opt/ffmpeg ';
    }


    function getInfos($url) {
        $cmd = getYoutubeDLCMD().'-s --restrict-filenames --get-title --get-thumbnail --get-duration --get-format ' . escapeshellarg($url) . ' 2>&1';
        $data = array();
        exec($cmd, $output, $ret);
        if($ret == 0) {
            $data['error'] = false; $i=0;
            if (strpos($output[$i], 'WARNING') !== false) $i++;
            $data['title'] = $output[$i];
            $data['tmb_url'] = $output[$i+1];
            $data['duration'] = $output[$i+2];
            $data['format'] = $output[$i+3];
        }
        else {
            $data['error'] = true;
            foreach($output as $out) {
                $data['message'] .= $out . '<br>'; 
            }
        }
        return $data;
    }


    function downloadVideo($url) {
        $video_id = getVideoID($url);
        $cmd = getYoutubeDLCMD().' -f \'bestvideo[height<=480]+bestaudio/best[height<=480]\' --merge-output-format mp4  -o ' . escapeshellarg($GLOBALS['folder'].'%(title)s-%(uploader)s.%(ext)s') . ' ' . escapeshellarg($url) . ' > '.$GLOBALS['folder'].$video_id.'.proc &';
        $data = array();    
        exec($cmd, $output, $ret);
        //$output[] = $cmd; $output[] = $video_id; $ret = 1;
        if($ret == 0)
        {
            $data['error'] = false;
        }
        else{
            $data['error'] = true;
            $data['message'] = "";
            foreach($output as $out) $data['message'] .= $out . '<br>'; 
        }
        return $data;
    }


    function commandsHandler() {
        if(isset($_GET['url']) && !empty($_GET['url']) && (!$GLOBALS['security'] || ($_SESSION['logged'] == 1)) )
        {
            $url = $_GET['url'];
            header('Content-type: application/json');
            if (isset($_GET['cmd'])) {
                if ($_GET['cmd']=='dl') echo json_encode(downloadVideo($url));
            } else echo json_encode(getInfos($url));
            exit;
        }
    }

    commandsHandler();

?>