<?php

$app['controllers']['tag/getall'] = function($app, $request) {

    $idProject = !empty($request['id_project']) ? (int)$request['id_project'] : null;

    if (!is_null($idProject)):
        $tags = $app['bugmanager']->getAllTagsFromProject($idProject);
        Response::responseWithSuccess(['tags' => $tags]);
    else:
        Response::responseWithError($app['i18n']['errors']['empty_id_project']);
    endif;

};
