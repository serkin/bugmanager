<?php

$app['controllers']['issue/delete'] = function($app, $request) {

    $idIssue = !empty($request['id_issue']) ? (int)$request['id_issue'] : null;

    if (empty($idIssue)):
        $result = false;
        $errorMsg = $app['i18n']['errors']['empty_issue_id'];
    else:
        $result = $app['bugmanager']->deleteIssue($idIssue);
        $errorMsg = $app['i18n']['errors']['cannot_update_issue_status'];
    endif;

    if ($result):
        Response::responseWithSuccess([], $app['i18n']['bugmanager']['issue_removed']);
    else:
        Response::responseWithError($errorMsg);
    endif;

};
