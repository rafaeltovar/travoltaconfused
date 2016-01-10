<?php
if (!defined('TEMPLATES_DIR'))
    define("TEMPLATES_DIR", ROOT_DIR."/src/_templates");

if (!defined('FILES_DIR'))
    define("FILES_DIR", ROOT_DIR."/files");

$app = new \Slim\Slim();

$app->get('/', function () {
    include_once(TEMPLATES_DIR."/home.php");
});

$app->post('/img/upload', function () use ($app) {
    // initialize variables
    $travolta_gif = sprintf("%s/src/_resource/travolta.gif", ROOT_DIR);
    $file_tmp = sprintf("/tmp/travolta_bg_%s%s", time(), rand(0,1000));

    // check if request is from this host (security)
    $available_hosts = array('www.travoltaconfused.com');
    if(!in_array($app->request->getHost(), $available_hosts))
        $app->halt(403, "You shall not pass!");

    // upload file
    file_put_contents($file_tmp, fopen('php://input', 'r'), FILE_APPEND);

    $travolta = new \Travolta\Travolta(ROOT_DIR, $file_tmp);
    $file_name = $travolta->exec();
    error_log($file_name);

    echo json_encode(array('filename' => $file_name.".gif"));
});

// get gif
$app->get('/img/:name', function ($name) use ($app) {
    $ext = substr(strrchr($name,'.'),1);
    if($ext=='gif')
        $file = sprintf("%s/%s", FILES_DIR, $name);
    else
        $file = sprintf("%s/%s.gif", FILES_DIR, $name);

    if(file_exists($file)) {
        $imginfo = getimagesize($file);
        //header("Content-Type: image/gif");
        //header('Content-Length: ' . filesize($file));
        $app->response->headers->set('Content-Type', 'image/gif');
        $app->response->headers->set('Content-Length', filesize($file));
        echo file_get_contents($file);
    } else
        $app->halt(404, "Not found!");
});

// download gif
$app->get('/img/:name/down', function ($name) use ($app) {
    $ext = substr(strrchr($name,'.'),1);
    if($ext=='gif')
        $file = sprintf("%s/%s", FILES_DIR, $name);
    else
        $file = sprintf("%s/%s.gif", FILES_DIR, $name);

    if(file_exists($file)) {
        $imginfo = getimagesize($file);
        //header("Content-Type: image/gif");
        //header('Content-Length: ' . filesize($file));
        $app->response->headers->set('Content-Type', 'image/gif');
        $app->response->headers->set('Content-Length', filesize($file));
        $app->response->headers->set('Content-Disposition', 'attachment; filename="'.$name.'.gif"');
        echo file_get_contents($file);
    } else
        $app->halt(404, "Not found!");
});

// get lasts
$app->get('/lasts', function () use ($app) {
    $files = \Travolta\Travolta::getFilesOrderByAdded(FILES_DIR);
    $newest_files = array_slice($files, 5, 6);

    include_once(TEMPLATES_DIR."/lasts.php");

});
// run php
$app->run();
?>
