<?php

$app['controllers']['issue/getone'] = function ($app, $request){

    $idProject  = !empty($request['id_project'])   ? $request['id_project']   : null;
    $idIssue    = !empty($request['id_issue'])     ? $request['id_issue']     : null;
    
    $result = true;

    if(empty($idProject)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_id_project'];
    elseif(empty($idIssue)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_issue_id'];
    else:
        
        $response = array();
        $response['users']  = $app['bugmanager']->getAllUsers();
        $response['tags']   = $app['bugmanager']->getAllTagsFromProject($idProject);
        $response['issue']  = $app['bugmanager']->getIssue($idIssue);
    endif;

    if($result):
        Response::responseWithSuccess($response);
    else:
        Response::responseWithError($errorMsg);
    endif;

};
