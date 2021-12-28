<?php
function autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require ("../PortX_Library/".$fileName);
}
spl_autoload_register('autoload');
$klein = new \Klein\Klein();
$klein->respond(function ($request, $response, $service, $app) use ($klein){
    $app->views="../PortX_Library/views";
    $app->api="../PortX_Library/Api";
    $app->report="../PortX_Library/Reports";
});

$klein->respond('GET', '/portx/', function ($request, $response,$service,$app) {
    $service->render("$app->views/landing.php");
});
$klein->with('/user', function () use ($klein) {
    $klein->respond(['GET','POST'],"/[:view]",function ($request,$response,$service,$app){
        new \Lib\Authentication\Agent($request,$response,$service,$app);
        $service->render("$app->views/$request->view.php",array('request'=>$request,'response'=>$response));
    });
});
$klein->with('portx/api', function () use ($klein) {
    $klein->respond(["GET","POST"],"/[:call]?/[:method]?/[:args]?",function ($request,$response,$service,$app){
        new \Lib\Authentication\Agent($request,$response,$service,$app);
        $method=$request->method;
        $call=explode('_',$request->call);
        $_call=array_map(function($d){
            return ucfirst($d);
        },$call);
        $call=implode('',$_call);
        $class="\\Api\\$call";
        $class_c=new $class($request,$response,$service,$app);
        if (!empty($method)) {
            $class_c->$method($request->args);
        }
    });

});
$klein->with("/report",function () use ($klein){
    $klein->respond(['GET', 'POST'],"/[:report]",function ($request,$response,$service,$app){
        new \Lib\Authentication\Agent($request,$response,$service,$app);
        $path = stream_resolve_include_path($app->report)."/$request->report";
        $response->file($path, $request->report);
    });
});

$klein->dispatch();
?>