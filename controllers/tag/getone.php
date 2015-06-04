<?php

$app['controllers']['tag/getone'] = function($app, $request) {

    $idTag = !empty($request['id_tag']) ? (int)$request['id_tag'] : null;

    if (!is_null($idTag)):
        $tag = $app['bugmanager']->getTag($idTag);
        Response::responseWithSuccess(['tag' => $tag]);
    else:
        Response::responseWithError($app['i18n']['errors']['empty_id_tag']);
    endif;

};
