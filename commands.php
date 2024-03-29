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
        //$cmd = getYoutubeDLCMD().' --merge-output-format mp4  -o ' . escapeshellarg($GLOBALS['folder'].'%(title)s-%(uploader)s.%(ext)s') . ' ' . escapeshellarg($url) . ' > '.$GLOBALS['folder'].$video_id.'.proc &';
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
        if(isset($_GET['logout']) && $_GET['logout'] == 1) endSession();
        if(isset($_GET['url']) && !empty($_GET['url']) && (!$GLOBALS['security'] || ($_SESSION['logged'] == 1)) )
        {
            $url = $_GET['url'];
            header('Content-type: application/json');
            if (isset($_GET['cmd'])) {
                if ($_GET['cmd']=='dl') echo json_encode(downloadVideo($url));
            } else echo json_encode(getInfos($url));
            exit;
        }
        if(isset($_GET['fileToDel'])) {
            $fileToDel = $_GET['fileToDel'];
            $data = array();    
            if(file_exists($GLOBALS['folder'].$fileToDel))
            {
                if(unlink($GLOBALS['folder'].$fileToDel))
                {
                    $data['error'] = false;
                    $data['message'] = "File deleted successfully.";
                    /*
                    echo '<div class="panel panel-success">';
                    echo '<div class="panel-heading"><h3 class="panel-title">Fichier à supprimer : '.$fileToDel.'</h3></div>';
                    echo '<div class="panel-body">Le fichier '.$fileToDel.' a été supprimé !</div>';
                    echo '</div>';
                    echo '<p><a href="'.$listPage.'">Go back</a></p>';
                */
                }
                else{
                    $data['error'] = true;
                    $data['message'] = "Error in deleting file.";
                /*
                    echo '<div class="panel panel-danger">';
                    echo '<div class="panel-heading"><h3 class="panel-title">Fichier à supprimer : '.$fileToDel.'</h3></div>';
                    echo '<div class="panel-body">Le fichier '.$fileToDel.' n\'a pas pu être supprimé !</div>';
                    echo '</div>';
                    echo '<p><a href="'.$listPage.'">Go back</a></p>';*/
                }
            } else {
                $data['error'] = true;
                $data['message'] = "The file do not exists.";
                /*
                echo '<div class="panel panel-danger">';
                echo '<div class="panel-heading"><h3 class="panel-title">Fichier à supprimer : '.$fileToDel.'</h3></div>';
                echo '<div class="panel-body">Le fichier '.$fileToDel.' ne peut pas être supprimé car il est introuvable !</div>';
                echo '</div>';
                echo '<p><a href="'.$listPage.'">Go back</a></p>';*/
            }
            $GLOBALS['popup']=$data;
            //header('Content-type: application/json');
            //echo json_encode($data);
            //exit;
        }
        /*
        elseif(!file_exists($folder))
        {
                echo '<div class="alert alert-danger">
                        <strong>Error : </strong> Destination folder doesn\'t exist or is not found here.
                    </div>';
        }*/
    }

    commandsHandler();

?>