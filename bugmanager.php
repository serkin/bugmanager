<?php
// Source: config/header.php


$app = array();

$app['config'] = array(
    'db' => array(
        'dsn'      => 'mysql:dbname=bugmanager;host=localhost',
        'user'      => 'root',
        'password'  => ''
    ),
    'url' => $_SERVER['PHP_SELF'],
    'debug' => false
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


    /**
     * Gets translation according with given code and idProject or all records
     * 
     * @param integer $idProject
     * @param string $code
     * @return array
     */
    public function getIssue($idIssue){/*

        $languages = $this->getLanguagesFromProject($idProject);

        $dbRecords = !is_null($code) ? $this->getCodeTranslation($idProject, $code) : array();


        $returnValue = array();
        $returnValue['code'] = $code;

        foreach ($languages as $lang):
            $returnValue['translations'][] = array(
                'language'      => $lang,
                'translation'   => !empty($dbRecords[$lang]) ? $dbRecords[$lang] : ''
            );
        endforeach;

        return $returnValue;   */   

    }
    
    public function getAllIssuesFromProject($idProject)
    {

        $sth = $this->dbh->prepare("SELECT * FROM `issue` WHERE `id_project` = ? and `status` = 'open'");
        $sth->bindParam(1, $idProject,  PDO::PARAM_INT);
        //$sth->bindParam(2, 'open',      PDO::PARAM_STR);
        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);

    }


    private function getAllReleasesFromProject($idProject)
    {/*
        $returnValue = array();
 
        $sth = $this->dbh->prepare('SELECT * FROM `translation` WHERE `id_project` = ? and `code` = ?');
        $sth->bindParam(1, $idProject, PDO::PARAM_INT);
        $sth->bindParam(2, $code, PDO::PARAM_STR);

        $sth->execute();

        foreach($sth->fetchAll(PDO::FETCH_ASSOC) as $record):
            $returnValue[$record['language']] = $record['translation'];
        endforeach;

        return $returnValue;*/
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
            $sth = $this->dbh->prepare('INSERT INTO `project` (`name`, `path`, `languages`) VALUES(?, ?, ?)');
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


    
    public function saveRelease($arr, $idRelease = null){}
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
        /*

        $languages = $this->getLanguagesFromProject($idProject);

        foreach ($languages as $language):
            
            if(isset($arr[$language])):
                $value = !empty($arr[$language]) ? $arr[$language] : '';
            
                $sth = $this->dbh->prepare('INSERT INTO `translation` (`id_project`, `code`, `language`, `translation`) VALUES(?, ?, ?, ?)'
                        . 'ON DUPLICATE KEY UPDATE `translation` = ?');
                
                $sth->bindParam(1, $idProject,  PDO::PARAM_INT);
                $sth->bindParam(2, $code,       PDO::PARAM_STR);
                $sth->bindParam(3, $language,   PDO::PARAM_STR);
                $sth->bindParam(4, $value,      PDO::PARAM_STR);
                $sth->bindParam(5, $value,      PDO::PARAM_STR);

                if($sth->execute() === false):
                    return false;
                endif;
            endif;
        endforeach;

        return true;*/
    }
    
    /**
     * @param integer $idProject
     * @param string $code
     * 
     * @return boolean
     */
    public function deleteIssue($idIssue)
    {/*
        $sth = $this->dbh->prepare('DELETE FROM `translation` WHERE `code` = ? and `id_project` = ?');

        $sth->bindParam(1, $code,       PDO::PARAM_STR);
        $sth->bindParam(2, $idProject,  PDO::PARAM_INT);
 
        return $sth->execute();
     * 
     */
    }
    
    public function deleteRelease($idRelease){}
    
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
        'name'              => 'Name',
        'description'              => 'Description',
        'manage'            => 'Manage',
        'clear'             => 'Clear',
        'add_project'       => 'Add/edit project',
        'delete'            => 'delete',
        'issue_search_placeholder'  => 'code',
        'save'              => 'Save'
        ),
    'bugmanager' => array(
        'project_saved' => 'Project saved!',
        'project_removed' => 'Project removed!',
    ),
    
    'errors' => array(
        'empty_id_project' => 'ID project not specified',
        'empty_project_name' => 'Project name not specified',
    )
);

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




$app['controllers']['translation/getone'] = function ($app, $request){
    
    $code       = !empty($request['code'])          ? $request['code']              : null;
    $idProject  = !empty($request['id_project'])    ? (int)$request['id_project']   : null;


    $result = $app['bugmanager']->getTranslation($idProject, $code);
    Response::responseWithSuccess($result);

};

// Source: controllers/issue/save.php


$isCodeValid = function($code) {

    return preg_match('/^[a-z0-9_\.]+$/', $code) === 1 ? true : false;
    
};

$app['controllers']['translation/save'] = function ($app, $request) use($isCodeValid) {

    parse_str(urldecode($request['form']), $form);


    $idProject  = !empty($form['id_project'])   ? $form['id_project']   : null;
    $code       = !empty($form['code'])         ? $form['code']         : null;
    $arr        = !empty($form['translation'])  ? $form['translation']  : array();

    if(empty($idProject)):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['empty_id_project'];
    elseif(empty($code) or $isCodeValid($code) === false):
        $result     = false;
        $errorMsg   = $app['i18n']['errors']['not_valid_project_code'];
    else:
        $result     = $app['bugmanager']->saveTranslation($idProject, $code, $arr);
        $error      = $app['bugmanager']->getError();
        $errorMsg   = $error[2];
    endif;

    if($result):
        Response::responseWithSuccess(array(), $app['i18n']['bugmanager']['translation_saved']);
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

// Source: controllers/release/delete.php


$app['controllers']['code/delete'] = function ($app, $request){

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

// Source: controllers/release/search.php




$app['controllers']['code/search'] = function ($app, $request){

    $keyword    = !empty($request['keyword'])       ? $request['keyword']           : null;
    $idProject  = !empty($request['id_project'])    ? (int)$request['id_project']   : null;

    $codes = $app['bugmanager']->getAllCodes($idProject, $keyword);
    Response::responseWithSuccess(array('codes' => $codes));

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
        </style>
        <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/0.8.1/mustache.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

    </head>
    <body onLoad="projects.reload()">

        <div class="row">
            <div class="col-md-8">
                <h4><?php echo $i18n['layout']['title']; ?></h4>
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

                <h3><?php echo $i18n['layout']['add_project']; ?></h3>
                <hr>
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

            </div>
            <div class="col-md-1"></div>
            <div class="col-md-2">
                <input type="text" class="form-control"
                       id="searchKeyword"
                       onkeyup="codes.SearchField.find($(this).val())"
                       placeholder="<?php echo $i18n['layout']['issue_search_placeholder']; ?>">
                <div id="issuesBlock"></div>
                <script id="issuesTemplate" type="x-tmpl-mustache">
                    <table class="table table-condensed">
                        <thead>
                            <th><?php echo $i18n['layout']['description']; ?></th>
                            <th><?php echo $i18n['layout']['manage']; ?></th>
                        </thead>
                        <tbody>
                            {{#issues}}
                                <tr OnClick="issues.selectIssue({{id_issue}}, $(this))" class="issue_block">
                                    <td>{{description}}</td>
                                    <td><button class="btn btn-danger btn-xs" OnClick="issues.deleteIssue({{id_issue}})"><?php echo $i18n['layout']['delete']; ?></button></td>
                                </tr>
                            {{/issues}}
                        </tbody>
                    </table>
                </script>

            </div>
            <div class="col-md-1"></div>
            <div class="col-md-4">
                <div id="translationFormBlock"></div>
                <script id="translationFormTemplate" type="x-tmpl-mustache">
                    {{#id_project}}
                        <div id="newTranslationButton">
                            <button type="button" onclick="translation.render()" class="btn btn-default"><?php echo $i18n['layout']['new_translation']; ?></button>
                        </div>

                        <form id="translationForm" class="form-horizontal">
                            <input type="hidden" name="id_project" value="{{id_project}}">

                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $i18n['layout']['code']; ?>:</label>
                                <div class="col-sm-6">
                                    <input class="form-control" type="text" name="code" id="code" value="{{code}}" required>
                                </div>
                            </div>

                            {{#translations}}
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{{language}}:</label>
                                    <div class="col-sm-6">
                                        <textarea class="form-control"  name="translation[{{language}}]">{{translation}}</textarea>
                                    </div>
                                </div>
                            {{/translations}}

                            <button type="button" onclick="translation.save($('#code').val())" class="btn btn-success"><?php echo $i18n['layout']['save']; ?></button>

                        </form>
                    {{/id_project}}
                </script>
            </div>
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

    render: function() {

        sendRequest('issue/getall', {id_project: idSelectedProject}, function(response){
            
            response.data.id_project = idSelectedProject;

            var template = $('#issuesTemplate').html();
            var rendered = Mustache.render(template, response.data);

            $('#issuesBlock').html(rendered);
        });

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

        issues.render();
        //codes.SearchField.show();

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

var codes = {

    deleteCode: function(code, searchKeyword) {
        sendRequest('code/delete', {code:code, id_project: idSelectedProject}, function(response){

            statusField.render(response.status);
            codes.SearchField.find(searchKeyword);
            translation.render();

        });

    },

    selectCode: function(code, el) {
        $('.code_block').removeClass('success');
        el.addClass('success');
        translation.render(code);
    },
    
    CodeForm: {
        save:   function(code) {
            sendRequest('code/save', {code:code, id_project: idSelectedProject});
        },
        render: function() {

            var template = $('#codeFormTemplate').html();
            var rendered = Mustache.render(template);

            $('#codeFormBlock').html(rendered);
        },
        hide:   function() {
            $('#codeFormBlock').html('');
        }
    },
    
    
    SearchField: {

        find:   function(keyword) {
            sendRequest('code/search', {keyword:keyword, id_project: idSelectedProject}, function(response){

            var template = $('#codesTemplate').html();
            var rendered = Mustache.render(template, response.data);

            $('#codesBlock').html(rendered);
        });
        },
        show: function() {
            $('#searchKeyword').show();
        },
        hide: function() {
            $('#searchKeyword').hide();
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
</script>
        <!-- /end -->

    </body>
</html>