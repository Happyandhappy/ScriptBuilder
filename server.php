<?php

    $htmlFile = "html.txt";
    $cssFile = "css.txt";
    $jsFile = "js.txt";

    // function saveTofile($content, $filename){
    //     $myfile = fopen($filename, "w") or die("Unable to open file!");        
    //     fwrite($myfile, $content);        
    //     fclose($myfile);
    // }

    function getFromfile($filename){
        if (!file_exists($filename) || filesize($filename) == 0) return "";
        $myfile = fopen($filename,'r');
        $data = fread($myfile,filesize($filename));
        fclose($myfile);
        // $data = readfile($filename);
        return $data;
    }



    
    $scriptContent = getFromfile($htmlFile);
    $cssContent = getFromfile($cssFile);
    $jsContent = getFromfile($jsFile);

    $data = array(
        "html" =>  $scriptContent,
        "css"  =>  $cssContent,
        "js"   =>  $jsContent
    );

    echo json_encode($data);
?>  