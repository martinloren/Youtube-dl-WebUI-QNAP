<?php

// Test if destination folder exists
function destFolderExists($destFolder)
{
    if(!file_exists($destFolder))
    {
        echo '<div class="alert alert-danger">
                <strong>Error : </strong> Destination folder doesn\'t exist or is not found here. 
            </div>';
    }
}

// Convert bytes to a more user-friendly value
function human_filesize($bytes, $decimals = 0)
{
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

function processProcFile($file) {
    $data_fields = array();
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if ($ext!="proc") return $data_fields;
    //echo $file.":<br>";
    $deleted = false; $last_dl_string="";
    $handle = fopen($file, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            // process the line read.
            if (strpos($line, 'Destination:') !== false) {
                $dest_file=trim(basename(explode(" ",$line)[2]));
                $ext2 = pathinfo($dest_file, PATHINFO_EXTENSION);
                if ($ext2 == "mp4") $data_fields['dest_video_file'] = $dest_file;
                if ($ext2 == "m4a") $data_fields['dest_audio_file'] = $dest_file;
            }
            if (strpos($line, 'Deleting original') !== false) {
                //Process completed, delete .proc file
                $deleted = true;
                break;
            }
            if (strpos($line, '[download]') !== false) {
                $lines=explode("\r",$line);
                $last_dl_string = end($lines);
            }
        }
        fclose($handle);
    } else {
        // error opening the file.
    } 
    if ($deleted) {
        //echo "Deleting: ".$file."<br/>";
        unlink($file);
        $data_fields['deleted'] = true;
    }
    if (!empty($last_dl_string)) {
        $arr = explode(" ", $last_dl_string);
        //print_r($arr);
        $data_fields['perc']=$arr[2];
        $data_fields['size']=$arr[4];
        $eta_pos = strpos($last_dl_string,"ETA");
        $data_fields['dl_string']=trim(substr($last_dl_string, 11, $eta_pos-11));
    }
    //$data_fields['dest_file'] = $dest_file;
    return $data_fields;
}

?>