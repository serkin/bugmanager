<?php


$app['controllers']['project/save'] = function ($app, $request) {

    parse_str($request['form'], $form);

    $idProject = !empty($form['id_project']) ? $form['id_project'] : null;

    if(empty($form['name'])):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_project_name'];
    else:
        $result     = $app['bugmanager']->saveProject($form, $idProject);
        $error      = $app['bugmanager']->getError();
        $errorMsg   = $error[2];
    endif;

    if($result):
        Response::responseWithSuccess(array('id_project' => $result), $app['i18n']['bugmanager']['project_saved']);
    else:
        Response::responseWithError($errorMsg);
    endif;
    
};