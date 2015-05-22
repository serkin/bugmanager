<?php

$app['controllers']['project/getall'] = function ($app, $request){
    
    $projects = $app['bugmanager']->getAllProjects();
    Response::responseWithSuccess(array('projects' => $projects));
    
};