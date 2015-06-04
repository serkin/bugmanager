<?php

$app['controllers']['tag/setstatus'] = function($app, $request) {

    $idTag = !empty($request['id_tag']) ? $request['id_tag'] : null;
    $status = !empty($request['status']) ? $request['status'] : null;

    if (empty($status)):
        $result = false;
        $errorMsg = $app['i18n']['errors']['empty_tag_status'];
    elseif (empty($idTag)):
        $result = false;
        $errorMsg = $app['i18n']['errors']['empty_id_tag'];
    else:
        $result = $app['bugmanager']->setTagStatus($idTag, $status);
        $errorMsg = $app['i18n']['errors']['cannot_update_tag_status'];
    endif;

    if ($result):
        Response::responseWithSuccess([], $app['i18n']['bugmanager']['tag_status_updated']);
    else:
        Response::responseWithError($errorMsg);
    endif;

};
