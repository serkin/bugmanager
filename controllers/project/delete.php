<?php



$app['controllers']['project/delete'] = function ($app, $request){


    $idProject = !empty($request['id_project']) ? (int)$request['id_project'] : null;    


    if(empty($idProject)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_id_project'];
    else:
        $result     = $app['bugmanager']->deleteProject($idProject);
        $error      = $app['bugmanager']->getError();
        $errorMsg   = $error[2];
    endif;


    if($result):
        Response::responseWithSuccess(array(), $app['i18n']['bugmanager']['project_removed']);
    else:
        Response::responseWithError($errorMsg);
    endif;

};