<?php


$app['controllers']['tag/save'] = function ($app, $request) {

    parse_str($request['form'], $form);

    $idTag      = !empty($form['id_tag'])    ? $form['id_tag']   : null;
    $version    = !empty($form['version'])   ? $form['version']  : null;
    $idProject = !empty($request['id_project']) ? (int)$request['id_project'] : null;

    if(empty($version)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_tag_version'];
    elseif(empty($idProject)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_id_project'];
    else:
        $result     = $app['bugmanager']->saveTag($version, $idProject, $idTag);
        $error      = $app['bugmanager']->getError();
        $errorMsg   = $error[2];
    endif;

    if($result):
        Response::responseWithSuccess(array('id_tag' => $result), $app['i18n']['bugmanager']['tag_saved']);
    else:
        Response::responseWithError($errorMsg);
    endif;
    
};