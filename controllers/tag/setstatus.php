<?php

$app['controllers']['issue/setstatus'] = function ($app, $request){

    $idIssue    = !empty($request['id_issue'])  ? (int)$request['id_issue']   : null;
    $status     = !empty($request['status'])    ? $request['status']          : null;

    if(empty($status)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_issue_status'];
    elseif(empty($idIssue)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_issue_id'];
    else:
        $result     = $app['bugmanager']->setIssuesStatus($idIssue, $status);
        $errorMsg   = $app['i18n']['errors']['cannot_update_issue_status'];
    endif;
    
    if($result):
        Response::responseWithSuccess(array(), $app['i18n']['bugmanager']['issue_status_updated']);
    else:
        Response::responseWithError($errorMsg);
    endif;

};