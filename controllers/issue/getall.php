<?php

$app['controllers']['issue/getall'] = function ($app, $request){

    $idProject = !empty($request['id_project']) ? (int)$request['id_project'] : null;

    if(!is_null($idProject)):
        $issues = $app['bugmanager']->getAllIssuesFromProject($idProject);
        Response::responseWithSuccess(array('issues' => $issues));
    else:
        Response::responseWithError($app['i18n']['errors']['empty_id_project']);
    endif;

};