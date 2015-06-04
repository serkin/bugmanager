<?php

$app['controllers']['tag/delete'] = function($app, $request) {

    $idTag = !empty($request['id_tag']) ? $request['id_tag'] : null;

    if (empty($idTag)):
        $result = false;
        $errorMsg = $app['i18n']['errors']['empty_id_tag'];
    else:
        $result = $app['bugmanager']->deleteTag($idTag);
        $error = $app['bugmanager']->getError();
        $errorMsg = $error[2];
    endif;

    if ($result):
        Response::responseWithSuccess([], $app['i18n']['bugmanager']['tag_removed']);
    else:
        Response::responseWithError($errorMsg);
    endif;

};
