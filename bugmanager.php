<?php
// Source: config/header.php


$app = array();

$app['config'] = array(
    'db' => array(
        'dsn'       => 'mysql:dbname=bugmanager;host=localhost',
        'user'      => 'bugmanager',
        'password'  => ''
    ),
    'url'           => $_SERVER['PHP_SELF'],
    'debug'         => false,
    'issue_types'   => array(
        array('type' => 'bug'),
        array('type' => 'feature')
    )
);



if($app['config']['debug']):
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
endif;


$app['locale'] = 'en';


// Source: classes/general/Bugmanager.php


/**
 * @author Serkin Alexander <serkin.alexander@gmail.com>
 */
class Bugmanager {

    
    /**
     *
     * @var PDO 
     */
    protected $dbh  = null;

    /**
     *
     * @var string
     */
    protected $dbDSN;
    
    /**
     *
     * @var string
     */
    protected $dbUser;
    
    /**
     *
     * @var string
     */
    protected $dbPassword;


    /**
     * 
     * @param string $dbDSN
     * @param string $dbUser
     * @param string $dbPassword
     */
    public function __construct($dbDSN, $dbUser, $dbPassword = '') {


        $this->dbDSN        = $dbDSN;
        $this->dbUser       = $dbUser;
        $this->dbPassword   = $dbPassword;

    }

    /**
     * Connects to database
     * 
     * @throws PDOException
     * @return void
     */
    public function connect(){
        $dbh = new PDO($this->dbDSN, $this->dbUser, $this->dbPassword);
        $this->dbh = $dbh;
        $this->dbh->exec('SET NAMES utf8');
    }
    

    public function getError()
    {
        return $this->dbh->errorInfo();
    }

    /**
     * Gets all projects
     * 
     * @return array
     */
    public function getAllProjects(){

        $sth = $this->dbh->prepare("SELECT * FROM `project`");
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }


    
    public function getAllIssuesFromProject($idProject)
    {

        $sth = $this->dbh->prepare("SELECT * FROM `issue` WHERE `id_project` = ? and `status` = 'open'");
        $sth->bindParam(1, $idProject,  PDO::PARAM_INT);
        //$sth->bindParam(2, 'open',      PDO::PARAM_STR);
        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getAllUsers()
    {

        $sth = $this->dbh->prepare("SELECT * FROM `user`");

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);

    }


    public function getAllTagsFromProject($idProject, $status = 'open')
    {
        $sth = $this->dbh->prepare("SELECT * FROM `tag` WHERE `id_project` = ? and `status` = ?");
        $sth->bindParam(1, $idProject,  PDO::PARAM_INT);
        $sth->bindParam(2, $status,     PDO::PARAM_STR);

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getIssue($idIssue)
    {

        $sth = $this->dbh->prepare("SELECT * FROM `issue` WHERE `id_issue` = ?");
        $sth->bindParam(1, $idIssue, PDO::PARAM_INT);

        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);

    }
    
    public function getTag($idTag)
    {

        $sth = $this->dbh->prepare("SELECT * FROM `tag` WHERE `id_tag` = ?");
        $sth->bindParam(1, $idTag, PDO::PARAM_INT);

        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * Get all information about project
     * 
     * @param integer $idProject
     * @return array
     */
    public function getProjectById($idProject)
    {
        $sth = $this->dbh->prepare("SELECT * FROM `project` WHERE `id_project` = ?");
        $sth->bindParam(1, $idProject, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Saves project
     * 
     * Edits project if $idProject was specified
     * 
     * @param array $arr
     * @param integer $idProject
     * @return int|boolean False on error
     */
    public function saveProject($arr, $idProject = null)
    {

        if(is_null($idProject)):
            $sth = $this->dbh->prepare('INSERT INTO `project` (`name`) VALUES(?)');
        else:
            $sth = $this->dbh->prepare('UPDATE `project` SET `name` = ? WHERE `id_project` = ?');
        endif;

        $sth->bindParam(1, $arr['name'], PDO::PARAM_STR);

        if(is_null($idProject)):
            $sth->execute();
            $returnValue = $this->dbh->lastInsertId() ? $this->dbh->lastInsertId() : 0;
        else:
            $sth->bindParam(2, $idProject, PDO::PARAM_INT);
            $sth->execute();
            $returnValue = $idProject;
        endif;

        return $returnValue;

    }

    public function setTagStatus($idTag, $status)
    {

        $sth = $this->dbh->prepare('UPDATE `tag` SET `status` = ? WHERE `id_tag` = ?');

        $sth->bindParam(1, $status, PDO::PARAM_STR);
        $sth->bindParam(2, $idTag,  PDO::PARAM_INT);

        return $sth->execute();

    }

    public function setIssuesStatus($idIssue, $status)
    {

        $sth = $this->dbh->prepare('UPDATE `issue` SET `status` = ? WHERE `id_issue` = ?');

        $sth->bindParam(1, $status,     PDO::PARAM_STR);
        $sth->bindParam(2, $idIssue,    PDO::PARAM_INT);        

        return $sth->execute();

    }

    /**
     * Removes project
     * 
     * @param int $idProject
     * 
     * @return boolean
     */
    public function deleteProject($idProject){
        
        $sql = "DELETE FROM `project` WHERE `id_project` = " . $idProject;
        return $this->dbh->exec($sql) ? true : false;
    }



    public function saveTag($version, $idProject, $idTag = null)
    {

        if(is_null($idTag)):
            $sth = $this->dbh->prepare('INSERT INTO `tag` (`version`, `id_project`) VALUES(?, ?)');
        else:
            $sth = $this->dbh->prepare('UPDATE `tag` SET `version` = ? WHERE `id_tag` = ?');
        endif;

        $sth->bindParam(1, $version, PDO::PARAM_STR);


        if(is_null($idTag)):
            $sth->bindParam(2, $idProject, PDO::PARAM_INT);
            $sth->execute();
            $returnValue = $this->dbh->lastInsertId() ? $this->dbh->lastInsertId() : 0;
        else:
            $sth->bindParam(2, $idTag, PDO::PARAM_INT);
            $sth->execute();
            $returnValue = $idTag;
        endif;

        return $returnValue;
    }
    /**
     * 
     * @param integer $idProject
     * @param string $code
     * @param array $arr
     * 
     * @return boolean
     */
    public function saveIssue($arr, $idIssue = null)
    {
        
        $arr['id_project']  = !empty($arr['id_project'])  ? $arr['id_project']    : null;
        $arr['id_tag']      = !empty($arr['id_tag'])      ? $arr['id_tag']        : null;
        $arr['description'] = !empty($arr['description']) ? $arr['description']   : null;
        $arr['type']        = !empty($arr['type'])        ? $arr['type']          : null;
        
        
        if(is_null($idIssue)):
            return $this->insertIssue($arr);
        else:
            return $this->updateIssue($arr, $idIssue);
        endif;
    }
    
    private function insertIssue($arr)
    {
        $sql = '
            INSERT INTO
                `issue` (
                    `id_project`,
                    `id_tag`,
                    `description`,
                    `type`
                )
            VALUES(?, ?, ?, ?)';
        $sth = $this->dbh->prepare($sql);
        
        $sth->bindParam(1, $arr['id_project'],  PDO::PARAM_INT);
        $sth->bindParam(2, $arr['id_tag'],      PDO::PARAM_INT);
        $sth->bindParam(3, $arr['description'], PDO::PARAM_STR);
        $sth->bindParam(4, $arr['type'],        PDO::PARAM_STR);

        $sth->execute();

        return $this->dbh->lastInsertId() ? $this->dbh->lastInsertId() : false;
    }
    
    private function updateIssue($arr, $idIssue)
    {

        $sql = '
            UPDATE
                `issue`
            SET
                `id_tag`        = ?,
                `description`   = ?,
                `type`          = ?
            WHERE
                `id_issue` = ?';

        $sth = $this->dbh->prepare($sql);

        $sth->bindParam(1, $arr['id_tag'],      PDO::PARAM_INT);
        $sth->bindParam(2, $arr['description'], PDO::PARAM_STR);
        $sth->bindParam(3, $arr['type'],        PDO::PARAM_STR);
        $sth->bindParam(4, $idIssue,            PDO::PARAM_INT);
        
        return $sth->execute();

    }

/**
     * @param integer $idIssue
     * 
     * @return boolean
     */
    public function deleteIssue($idIssue)
    {
        $sth = $this->dbh->prepare('DELETE FROM `issue` WHERE `id_issue` = ?');

        $sth->bindParam(1, $idIssue, PDO::PARAM_INT);
 
        return $sth->execute();
     
    }
    
    public function deleteTag($idTag)
    {
        
    }
    
}

// Source: classes/general/Response.php


/**
 * Class creates two types of response according having error
 * @author Serkin Alexander <serkin.alexander@gmail.com>
 */
class Response {

    public static function responseWithError($message){

        header('Content-Type: application/json');

        $response = array(
            'status' => array(
                'state'     => 'notOk',
                'message'   => $message
                ),
            'data'  => array()
                );

        echo json_encode($response);
        die();

    }

    public static function responseWithSuccess($arr, $statusMessage = ''){

        header('Content-Type: application/json');

        $response = array(
            'status' => array(
                'state'     => 'Ok',
                'message'   => $statusMessage
                ),
            'data'  => $arr
                );

        echo json_encode($response);
        die();
    }
}

// Source: i18n/en.php



$app['i18n']['en'] = array(
    'layout' => array(
        'title'             => 'Bugmanager',
        'add_tag'       => 'Add/edit release/tag',
        'tags'          => 'Releases/Tags',
        'name'              => 'Name',
        'version'           => 'Release version',
        'description'       => 'Description',
        'manage'            => 'Manage',
        'clear'             => 'Clear',
        'add_project'       => 'Add/edit project',
        'delete'            => 'delete',
        'tag'           => 'Release/Tag',
        'assigned_to_user'  => 'Assigned to user',
        'type'              => 'Type',
        'issue_search_placeholder'  => 'code',
        'save'              => 'Save'
        ),
    'bugmanager' => array(
        'project_saved' => 'Project saved!',
        'project_removed' => 'Project removed!',
        'issue_status_updated' => 'Issue status updated',
        'issue_removed' => 'Issue removed!',
        'issue_saved' => 'Issue saved!',
        'tag_saved' => 'Tag saved!',
        'tag_status_updated' => 'Tag status updated!',
    ),
    
    'errors' => array(
        'empty_id_project' => 'ID project not specified',
        'empty_project_name' => 'Project name not specified',
        'empty_issue_status' => 'Issue status not specified',
        'empty_issue_id' => 'Issue ID not specified',
        'empty_id_tag' => 'Tag ID not specified',
        'empty_tag_status' => 'Tag status not specified',
        'empty_tag_version' => 'Tag version not specified',
        'cannot_update_issue_status' => 'Cannot update issue status',
        'cannot_update_tag_status' => 'Cannot update tag status',
    )
);

// Source: controllers/issue/delete.php


$app['controllers']['issue/delete'] = function ($app, $request){

    $idIssue = !empty($request['id_issue']) ? (int)$request['id_issue'] : null;

    if(empty($idIssue)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_issue_id'];
    else:
        $result     = $app['bugmanager']->deleteIssue($idIssue);
        $errorMsg   = $app['i18n']['errors']['cannot_update_issue_status'];
    endif;
    
    if($result):
        Response::responseWithSuccess(array(), $app['i18n']['bugmanager']['issue_removed']);
    else:
        Response::responseWithError($errorMsg);
    endif;

};

// Source: controllers/issue/getall.php


$app['controllers']['issue/getall'] = function ($app, $request){

    $idProject = !empty($request['id_project']) ? (int)$request['id_project'] : null;

    if(!is_null($idProject)):
        $issues = $app['bugmanager']->getAllIssuesFromProject($idProject);
        Response::responseWithSuccess(array('issues' => $issues));
    else:
        Response::responseWithError($app['i18n']['errors']['empty_id_project']);
    endif;

};

// Source: controllers/issue/getone.php


$app['controllers']['issue/getone'] = function ($app, $request){

    $idProject  = !empty($request['id_project'])   ? $request['id_project']   : null;
    $idIssue    = !empty($request['id_issue'])     ? $request['id_issue']     : null;
    
    $result = true;
    $response = array();
    $response['issue_types'] = $app['config']['issue_types'];

    if(empty($idProject)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_id_project'];
    endif;

    $response['tags']   = !empty($idProject)    ? $app['bugmanager']->getAllTagsFromProject($idProject) : array();
    $response['issue']  = !empty($idIssue)      ? $app['bugmanager']->getIssue($idIssue)                : array();


    if($result):
        Response::responseWithSuccess($response);
    else:
        Response::responseWithError($errorMsg);
    endif;

};


// Source: controllers/issue/save.php



$app['controllers']['issue/save'] = function ($app, $request) {

    parse_str(urldecode($request['form']), $arr);


    $idIssue            = !empty($arr['id_issue'])          ? $arr['id_issue']              : null;
    $arr['id_project']  = !empty($request['id_project'])    ? (int)$request['id_project']   : null;

    if(empty($arr['id_project'])):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_id_project'];
    else:
        $result     = $app['bugmanager']->saveIssue($arr, $idIssue);
        $error      = $app['bugmanager']->getError();
        $errorMsg   = $error[2];
    endif;

    if($result):
        Response::responseWithSuccess(array(), $app['i18n']['bugmanager']['issue_saved']);
    else:
        Response::responseWithError($errorMsg);
    endif;

};

// Source: controllers/issue/setstatus.php


$app['controllers']['issue/setstatus'] = function ($app, $request){

    $idIssue    = !empty($request['id_issue'])  ? (int)$request['id_issue']   : null;
    $status     = !empty($request['status'])    ? $request['status']          : null;

    if(empty($status)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_issue_status'];
    elseif(empty($idIssue)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_issue_id'];
    else:
        $result     = $app['bugmanager']->setIssuesStatus($idIssue, $status);
        $errorMsg   = $app['i18n']['errors']['cannot_update_issue_status'];
    endif;
    
    if($result):
        Response::responseWithSuccess(array(), $app['i18n']['bugmanager']['issue_status_updated']);
    else:
        Response::responseWithError($errorMsg);
    endif;

};

// Source: controllers/project/delete.php




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

// Source: controllers/project/getall.php


$app['controllers']['project/getall'] = function ($app, $request){
    
    $projects = $app['bugmanager']->getAllProjects();
    Response::responseWithSuccess(array('projects' => $projects));
    
};

// Source: controllers/project/getone.php




$app['controllers']['project/getone'] = function ($app, $request){
    
    $idProject = !empty($request['id_project']) ? (int)$request['id_project'] : null;

    if(!is_null($idProject)):
        $project = $app['bugmanager']->getProjectByID($idProject);
        Response::responseWithSuccess(array('project' => $project));
    else:
        Response::responseWithError($app['i18n']['errors']['empty_id_project']);
    endif;

};

// Source: controllers/project/save.php



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

// Source: controllers/tag/delete.php


$app['controllers']['tag/delete'] = function ($app, $request){

    $idProject  = !empty($request['id_project'])    ? (int)$request['id_project']   : null;
    $code       = !empty($request['code'])          ? $request['code']              : null;


    if(empty($idProject)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_id_project'];
    elseif(empty($code)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_code'];
    else:
        $result     = $app['bugmanager']->deleteCode($idProject, $code);
        $error      = $app['bugmanager']->getError();
        $errorMsg   = $error[2];
    endif;


    if($result):
        Response::responseWithSuccess(array(), $app['i18n']['bugmanager']['code_removed']);
    else:
        Response::responseWithError($errorMsg);
    endif;
    
};

// Source: controllers/tag/getall.php


$app['controllers']['tag/getall'] = function ($app, $request){
    
    $idProject = !empty($request['id_project']) ? (int)$request['id_project'] : null;

    if(!is_null($idProject)):
        $tags = $app['bugmanager']->getAllTagsFromProject($idProject);
        Response::responseWithSuccess(array('tags' => $tags));
    else:
        Response::responseWithError($app['i18n']['errors']['empty_id_project']);
    endif;
    
};

// Source: controllers/tag/getone.php


$app['controllers']['tag/getone'] = function ($app, $request){
    
    $idTag = !empty($request['id_tag']) ? (int)$request['id_tag'] : null;

    if(!is_null($idTag)):
        $tag = $app['bugmanager']->getTag($idTag);
        Response::responseWithSuccess(array('tag' => $tag));
    else:
        Response::responseWithError($app['i18n']['errors']['empty_id_tag']);
    endif;

};

// Source: controllers/tag/save.php



$app['controllers']['tag/save'] = function ($app, $request) {

    parse_str($request['form'], $form);

    $idTag      = !empty($form['id_tag'])           ? $form['id_tag']               : null;
    $version    = !empty($form['version'])          ? $form['version']              : null;
    $idProject  = !empty($request['id_project'])    ? (int)$request['id_project']   : null;

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

// Source: controllers/tag/setstatus.php


$app['controllers']['tag/setstatus'] = function ($app, $request){

    $idTag      = !empty($request['id_tag'])    ? $request['id_tag'] : null;
    $status     = !empty($request['status'])    ? $request['status'] : null;

    if(empty($status)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_tag_status'];
    elseif(empty($idTag)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_id_tag'];
    else:
        $result     = $app['bugmanager']->setTagStatus($idTag, $status);
        $errorMsg   = $app['i18n']['errors']['cannot_update_tag_status'];
    endif;
    
    if($result):
        Response::responseWithSuccess(array(), $app['i18n']['bugmanager']['tag_status_updated']);
    else:
        Response::responseWithError($errorMsg);
    endif;

};

// Source: config/footer.php



$app['bugmanager'] = new Bugmanager($app['config']['db']['dsn'], $app['config']['db']['user'], $app['config']['db']['password'], $app['i18n']);

try {
    $app['bugmanager']->connect();
} catch (Exception $exc){
    Response::responseWithError($exc->getMessage());
}

$i18n = $app['i18n'] = $app['i18n'][$app['locale']];


if(!empty($_REQUEST['action']) && isset($app['controllers'][$_REQUEST['action']])):
    $app['controllers'][$_REQUEST['action']]($app, $_REQUEST);
endif;

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo $i18n['layout']['title']; ?></title>

        <style>
            html {
                margin: 0;
                padding: 0;
            }
            table {
                border: #cecece 1px solid;
            }
            
            .selected_project {
                background-color: #cecece;
            }
            #status_field {
                height: 30px;
                padding: 5px;
                margin: 5px;
            }
            #status_field p {
                padding: 10px;
                font-size: 14pt;
            }
            #codesBlock {
                margin-top: 10px;
            }
            #newTranslationButton {
                margin-bottom: 10px;
            }
            #searchKeyword {
                display: none;
            }
            fieldset.scheduler-border {
    border: 1px groove #ddd !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 10px 0 1.5em 0 !important;
    -webkit-box-shadow:  0px 0px 0px 0px #000;
            box-shadow:  0px 0px 0px 0px #000;
}
            legend.scheduler-border {
                font-size: 12pt;
    width:inherit; /* Or auto */
    padding:0 10px; /* To give a bit of padding on the left and right */
    border-bottom:none;
}
        </style>
        <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/0.8.1/mustache.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

    </head>
    <body onLoad="projects.reload()">

        <div class="row">
            <div class="col-md-8">
                <h4><a href="https://github.com/serkin/bugmanager" target="_blank"><?php echo $i18n['layout']['title']; ?></a></h4>
            </div>
            <div class="col-md-3">
                <div id="status_field">&nbsp;</div>
            </div>
            <div class="col-md-12">
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-3">
                <div id="projectsBlock"></div>

                <script id="projectsTemplate" type="x-tmpl-mustache">
                    <table class="table table-condensed">
                        <thead>
                            <th>#</th>
                            <th><?php echo $i18n['layout']['name']; ?></th>
                            <th><?php echo $i18n['layout']['manage']; ?></th>
                        </thead>
                        <tbody>
                            {{#projects}}
                                <tr OnClick="projects.selectProjectById({{id_project}})" class="project_block" id="project_block_{{id_project}}">
                                    <td>{{id_project}}</td>
                                    <td>{{name}}</td>
                                    <td><button class="btn btn-danger btn-xs" OnClick="projects.deleteProject({{id_project}})"><?php echo $i18n['layout']['delete']; ?></button></td>
                                </tr>
                            {{/projects}}
                                    
                            {{^projects}}
                                <tr>
                                    <td colspan="4">{{i18n.no_projects}}</td>
                                </tr>
                            {{/projects}}
                            
                        </tbody>
                    </table>
                </script>

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?php echo $i18n['layout']['add_project']; ?></legend>

                    <div id="projectFormBlock"></div>
                    <script id="projectFormTemplate" type="x-tmpl-mustache">
                        <form id="projectForm" class="form-horizontal">
                            {{#id_project}}
                                <input type="hidden" name="id_project" value="{{id_project}}">
                            {{/id_project}}

                            <div class="form-group">
                                <label class="col-sm-6 control-label"><?php echo $i18n['layout']['name']; ?></label>
                                <div class="col-sm-6">
                                    <input class="form-control input-sm" type="text" name="name" value="{{name}}">
                                </div>
                            </div>
                            <button type="button" onclick="projects.ProjectForm.save()" class="btn btn-success"><?php echo $i18n['layout']['save']; ?></button>
                            <button type="reset" onclick="projects.ProjectForm.render()" class="btn btn-default"><?php echo $i18n['layout']['clear']; ?></button>
                        </form>
                    </script>
                </fieldset>
                
                
                
                    <div id="tagsBlock"></div>

                    <script id="tagsTemplate" type="x-tmpl-mustache">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?php echo $i18n['layout']['tags']; ?></legend>
                            <table class="table table-condensed">
                                <thead>
                                    <th>#</th>
                                    <th><?php echo $i18n['layout']['name']; ?></th>
                                    <th><?php echo $i18n['layout']['manage']; ?></th>
                                </thead>
                                <tbody>
                                    {{#tags}}
                                        <tr OnClick="tags.selectTag({{id_tag}}, $(this))" class="tag_block" id="tag_block_{{id_tag}}">
                                            <td>{{id_tag}}</td>
                                            <td>{{version}}</td>
                                            <td>
                                                <button class="btn btn-success btn-xs" OnClick="tags.setTagStatus({{id_tag}},'released')">release</button>
                                                <button class="btn btn-danger btn-xs" OnClick="tags.deleteTag({{id_tag}})"><?php echo $i18n['layout']['delete']; ?></button>
                                            </td>
                                        </tr>
                                    {{/tags}}

                                    {{^tags}}
                                        <tr>
                                            <td colspan="4">{{i18n.no_tags}}</td>
                                        </tr>
                                    {{/tags}}

                                </tbody>
                            </table>
                        </fieldset>
                
                        
                    </script>
                    <div id="tagFormBlock"></div>

                    <script id="tagFormTemplate" type="x-tmpl-mustache">
                                                
                                                <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?php echo $i18n['layout']['tags']; ?></legend>
                            
                        <form id="tagForm" class="form-horizontal">
                            {{#id_tag}}
                                <input type="hidden" name="id_tag" value="{{id_tag}}">
                            {{/id_tag}}

                            <div class="form-group">
                                <label class="col-sm-6 control-label"><?php echo $i18n['layout']['version']; ?></label>
                                <div class="col-sm-6">
                                    <input class="form-control input-sm" type="text" name="version" value="{{version}}">
                                </div>
                            </div>
                            <button type="button" onclick="tags.TagForm.save()" class="btn btn-success"><?php echo $i18n['layout']['save']; ?></button>
                            <button type="reset" onclick="tags.TagForm.render()" class="btn btn-default"><?php echo $i18n['layout']['clear']; ?></button>
                        </form>
                </fieldset>
                    </script>

            </div>
            <div class="col-md-1"></div>
            <div class="col-md-5">
                <div id="issuesBlock"></div>
                <script id="issuesTemplate" type="x-tmpl-mustache">
                    <table class="table table-condensed">
                        <thead>
                            <th>#</th>
                            <th><?php echo $i18n['layout']['description']; ?></th>
                            <th><?php echo $i18n['layout']['manage']; ?></th>
                        </thead>
                        <tbody>
                            {{#issues}}
                                <tr OnClick="issues.selectIssue({{id_issue}}, $(this))" class="issue_block" id="issue_block_{{id_issue}}">
                                    <td>{{id_issue}}</td>
                                    <td>{{description}}</td>
                                    <td>
                                        <button class="btn btn-success btn-xs" OnClick="issues.setIssueStatus({{id_issue}},'closed')">close</button>
                                        <button class="btn btn-danger btn-xs" OnClick="issues.deleteIssue({{id_issue}})"><?php echo $i18n['layout']['delete']; ?></button>
                                    </td>
                                </tr>
                            {{/issues}}
                        </tbody>
                    </table>
                </script>
                
                <div id="issueFormBlock"></div>
                <script id="issueFormTemplate" type="x-tmpl-mustache">
                    <form id="issueForm" class="form-horizontal">
                        {{#issue.id_issue}}
                            <input type="hidden" name="id_issue" value="{{issue.id_issue}}">
                        {{/issue.id_issue}}
            
                        <div class="form-group">
                            <label class="col-sm-6 control-label"><?php echo $i18n['layout']['description']; ?></label>
                            <div class="col-sm-6">
                                <textarea class="form-control input-sm" name="description">{{issue.description}}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-6 control-label"><?php echo $i18n['layout']['type']; ?></label>
                            <div class="col-sm-6">
                                <select class="form-control input-sm" name="type">
                                    {{#issue_types}}
                                        <option {{#selected}}selected{{/selected}} value="{{type}}">{{type}}</option>
                                    {{/issue_types}}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-6 control-label"><?php echo $i18n['layout']['tag']; ?></label>
                            <div class="col-sm-6">
                                <select class="form-control input-sm" name="id_tag">
                                    <option value=""></option>
                                    {{#tags}}
                                        <option {{#selected}}selected{{/selected}} value="{{id_tag}}">{{version}}</option>
                                    {{/tags}}
                                </select>
                            </div>
                        </div>
                        <button type="button" onclick="issues.IssueForm.save()" class="btn btn-success"><?php echo $i18n['layout']['save']; ?></button>
                        <button type="reset" onclick="issues.IssueForm.render()" class="btn btn-default"><?php echo $i18n['layout']['clear']; ?></button>
                    </form>
                </script>

            </div>
            <div class="col-md-2"></div>

        </div>
        
        <div class="row">
            <div class="col-md-12">
                <hr>
            </div>
        </div>
        <div class="row">

            <div class="col-md-10">
                &nbsp;
            </div>
            <div class="col-md-2">
                <a href="https://github.com/serkin/foler" target="_blank">github</a>
            </div>

        </div>
        <script>
            url = '<?php echo $app["config"]["url"]; ?>';
        </script>

        <!-- Here goes content from all js/*.js files -->
        <script>


function sendRequest(action, data, callback) {

    $.ajax({
            type: 'post',
            url: url + '?action='+action,
            data: data,
            error: function()	{
                alert('Connection lost');
            },
            success: function(response)	{
                if(callback !== "undefined"){
                    callback(response);
                }
            }
        });
}

var locale = 'en';
var i18n = {
    en: {
        no_projects: 'No projects yet. Start with adding new project in the form below',
        no_tags: 'No tags yet.',
        no_codes: 'No codes found.',
    }
};

i18n = i18n[locale];


 


var issues = {
    save: function(code) {

        var data = $('#translationForm').serialize();

        sendRequest('translation/save', {form:data}, function(response){
            statusField.render(response.status);
            translation.render(code);
        });

    },

    reload: function() {

        sendRequest('issue/getall', {id_project: idSelectedProject}, function(response){
            
            response.data.id_project = idSelectedProject;

            var template = $('#issuesTemplate').html();
            var rendered = Mustache.render(template, response.data);

            $('#issuesBlock').html(rendered);
        });

    },
    setIssueStatus: function(idIssue, status) {

        sendRequest('issue/setstatus', {id_issue: idIssue, status: status}, function(response){
            statusField.render(response.status);

            if(response.status.state === 'Ok'){
                issues.reload();
            }
        });
    },
    deleteIssue: function(idIssue) {

        sendRequest('issue/delete', {id_issue: idIssue}, function(response){
            statusField.render(response.status);

            if(response.status.state === 'Ok'){
                issues.reload();
            }
        });
    },
    selectIssue: function(idIssue) {

        $('.issue_block').removeClass('success');
        $('#issue_block_' + idIssue).addClass('success');

        issues.IssueForm.render(idIssue);

    },
    IssueForm: {
        save: function(){
            var data = $('#issueForm').serialize();

            sendRequest('issue/save', {form: data, id_project: idSelectedProject}, function(response){

                statusField.render(response.status);

                if(response.status.state === 'Ok'){
                    issues.reload();

                }

            });
        },

        render: function(idIssue) {

            var template = $('#issueFormTemplate').html();
            

            if(idIssue === undefined) {
                var idIssue = null;
            }


            sendRequest('issue/getone',{id_project: idSelectedProject, id_issue: idIssue}, function(response){


                // Select type

                for (var i in response.data.issue_types) {

                    if(response.data.issue_types[i].type === response.data.issue.type) {
                        response.data.issue_types[i].selected = true;
                    }
                }

                // Select release

                for (var i in response.data.tags) {

                    if(response.data.tags[i].id_tag === response.data.issue.id_tag) {
                        response.data.tags[i].selected = true;
                    }
                }

                var rendered = Mustache.render(template, response.data);
                $('#issueFormBlock').html(rendered);

            });
        }
    }
};
var idSelectedProject;
var projects = {
    deleteProject: function(idProject) {

        sendRequest('project/delete', {id_project: idProject}, function(response){
            statusField.render(response.status);
            translation.render();
            projects.reload();
            idSelectedProject = null;

        });
    },
    reload: function() {
        sendRequest('project/getall',{}, function(response){

            response.data.i18n = i18n;
            var template = $('#projectsTemplate').html();
            var rendered = Mustache.render(template, response.data);

            $('#projectsBlock').html(rendered);
        });

        projects.ProjectForm.render();
    },

    selectProjectById: function(idProject) {

        $('.project_block').removeClass('success');
        $('#project_block_' + idProject).addClass('success');
        idSelectedProject = parseInt(idProject);
        projects.ProjectForm.render(idSelectedProject);

        issues.reload();
        issues.IssueForm.render();
        tags.reload();

    },
    export: function(idProject, type, ev) {
        sendRequest('project/export', {id_project: idProject, type: type}, function(response){
                statusField.render(response.status);
        });
        ev.stopPropagation();
    },

    
    ProjectForm: {
        save: function(){
            var data = $('#projectForm').serialize();

            sendRequest('project/save', {form: data}, function(response){

                statusField.render(response.status);

                if(response.status.state === 'Ok'){
                    projects.reload();

                    var id = parseInt(response.data.id_project);

                    if(id > 0){
                        projects.selectProjectById(id);
                    }
                }

            });
        },

        render: function(idProject) {

            var template = $('#projectFormTemplate').html();

            if(idProject === undefined) {

                var rendered = Mustache.render(template);
                $('#projectFormBlock').html(rendered);

            } else {

                sendRequest('project/getone',{id_project:idProject}, function(response){

                    var rendered = Mustache.render(template, response.data.project);
                    $('#projectFormBlock').html(rendered);
                });
            }
        }
    }
};

var statusField = {
    el: $('#status_field'),
    setFail: function(message) {
        this.el.html('<p class="bg-danger">'+message+'</p>');
        setTimeout(function() {
            statusField.clear();
        }, 5000);
    },
    
    setOk: function(message) {
        this.el.html('<p class="bg-success">'+message+'</p>');
        setTimeout(function() {
            statusField.clear();
        }, 5000);
    },
    
    clear: function() {
        this.el.html('');
    },
    
    render: function(status) {

        if(status.state === 'Ok') {
            this.setOk(status.message);
        }
        
        if(status.state === 'notOk') {
            this.setFail(status.message);
        }
    }
};

var tags = {

    deleteTag: function(idTag) {
        sendRequest('tag/delete', {id_tag:idTag, id_project: idSelectedProject}, function(response){

            //statusField.render(response.status);

        });

    },

    selectTag: function(idTag, el) {
        $('.tag_block').removeClass('success');
        el.addClass('success');
        
        tags.TagForm.render(idTag);

    },

    TagForm: {
        save:   function() {

            var data = $('#tagForm').serialize();
            sendRequest('tag/save', {form: data, id_project: idSelectedProject}, function(response) {
                statusField.render(response.status);
                tags.reload();
                
            });
        },
        render: function(idTag) {

            var template = $('#tagFormTemplate').html();

            if(idTag === undefined) {

                var rendered = Mustache.render(template);
                $('#tagFormBlock').html(rendered);

            } else {

                sendRequest('tag/getone',{id_tag:idTag}, function(response){

                    var rendered = Mustache.render(template, response.data.tag);
                    $('#tagFormBlock').html(rendered);
                });
            }
        },
        hide:   function() {
            $('#tagFormBlock').html('');
        }
    },
    setTagStatus: function(idTag, status) {

        sendRequest('tag/setstatus', {id_tag: idTag, status: status}, function(response){
            statusField.render(response.status);

            if(response.status.state === 'Ok'){
                tags.reload();
            }
        });
    },
    reload: function() {
        sendRequest('tag/getall',{ id_project: idSelectedProject}, function(response){

            response.data.i18n = i18n;
            var template = $('#tagsTemplate').html();
            var rendered = Mustache.render(template, response.data);

            $('#tagsBlock').html(rendered);
        });

        tags.TagForm.render();
    }
};
</script>
        <!-- /end -->

    </body>
</html>