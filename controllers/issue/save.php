<?php


$app['controllers']['issue/save'] = function($app, $request) {

    parse_str(urldecode($request['form']), $arr);

    $idIssue = !empty($arr['id_issue']) ? $arr['id_issue'] : null;
    $arr['id_project'] = !empty($request['id_project']) ? (int)$request['id_project'] : null;

    if (empty($arr['id_project'])):
        $result = false;
        $errorMsg = $app['i18n']['errors']['empty_id_project'];
    else:
        $result = $app['bugmanager']->saveIssue($arr, $idIssue);
        $error = $app['bugmanager']->getError();
        $errorMsg = $error[2];
    endif;

    if ($result):
        Response::responseWithSuccess([], $app['i18n']['bugmanager']['issue_saved']);
    else:
        Response::responseWithError($errorMsg);
    endif;

};
