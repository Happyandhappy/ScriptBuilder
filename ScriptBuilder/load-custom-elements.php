<?php

$ser_url = "Script_Builder_dev";

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

}


function getTableData(){
    $url = "{$_SERVER['SERVER_NAME']}/{$GLOBALS['ser_url']}/list.php?token=6D52F8F1-4600-4D5F-8B0A-02BA9F430916&id=10&Container=";    
    $ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response);
}


$path = __DIR__.'/elements';

$elements = array();

/*
    get container lists & each table list
*/
$files = new DirectoryIterator($path);
$tabledata = getTableData();
$containerlist = preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/','$1:',json_encode($tabledata->container)) ;
$dtlist = preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/','$1:',json_encode($tabledata->lists));

foreach ($files as $file) {
    if ( ! $file->isDot()) {
        $contents = file_get_contents($path.'/'.$file->getFilename());

        preg_match('/<script>(.+?)<\/script>/s', $contents, $config);
        preg_match('/<style.*?>(.+?)<\/style>/s', $contents, $css);
        preg_match('/<\/style.*?>(.+?)<script>/s', $contents, $html);        

        if (!isset($config[1]) || !isset($html[1])) {
            continue;
        }
        
        $config[1] = str_replace('dtlist:[]', "dtlist:" . $dtlist, $config[1], $i);
        $config[1] = str_replace('list:[{}]', "list:" . $containerlist, $config[1], $i);

        $elements[] = array(
            'css' => isset($css[1]) ? trim($css[1]) : '',
            'html' => trim($html[1]),
            'config' => trim($config[1])
        );
    }
}

echo json_encode($elements);