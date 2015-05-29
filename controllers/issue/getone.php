<?php

$app['controllers']['issue/getone'] = function ($app, $request){

    $idProject  = !empty($request['id_project'])   ? $request['id_project']   : null;
    $idIssue    = !empty($request['id_issue'])     ? $request['id_issue']     : null;
    
    $result = true;
    $response = array();
    $response['issue_types'] = $app['config']['issue_types'];

    if(empty($idProject)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_id_project'];
    endif;

    $response['tags']   = !empty($idProject)    ? $app['bugmanager']->getAllTagsFromProject($idProject) : array();
    $response['issue']  = !empty($idIssue)      ? $app['bugmanager']->getIssue($idIssue)                : array();


    if($result):
        Response::responseWithSuccess($response);
    else:
        Response::responseWithError($errorMsg);
    endif;

};
