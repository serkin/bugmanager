<?php

require_once dirname(dirname(__DIR__)) . '/classes/general/Bugmanager.php';



class BugmanagetTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var bugmanager
     */
    public $bugmanager;

    /**
     * @var array
     */
    public $project;


    /**
     * @var PDOStatement
     */
    public $dbh;

    public function setUp()
    {


        parent::setUp();
        $this->bugmanager = new Bugmanager($GLOBALS['db_dsn'], $GLOBALS['db_user'], $GLOBALS['db_password']);
        $this->bugmanager->connect();

        $this->dbh = new PDO($GLOBALS['db_dsn'], $GLOBALS['db_user'], $GLOBALS['db_password']);
        $this->dbh->exec('SET NAMES utf8');
        $this->truncateDB();

        $this->project = [
            'name' => 'project 1',
        ];

    }

    public function truncateDB()
    {

        foreach($this->bugmanager->getAllProjects() as $project) {
            $this->bugmanager->deleteProject($project['id_project']);
        }
    }


    public function insertProject()
    {
        return $this->bugmanager->saveProject($this->project['name']);
    }


    public function testInsertingProject()
    {

        $idProject = $this->bugmanager->saveProject($this->project['name']);
        $project = $this->bugmanager->getProjectById($idProject);

        $this->assertTrue(is_numeric($idProject));
        $this->assertEquals($project['name'], $this->project['name']);

        // Updating project

        $idProject2 = $this->bugmanager->saveProject($this->project['name'].'0', $idProject);
        $project = $this->bugmanager->getProjectById($idProject2);
        $this->assertEquals($project['name'], $this->project['name'].'0');

    }


    public function testGetAllProjects()
    {
        $this->insertProject();

        $projects = $this->bugmanager->getAllProjects();
        $this->assertCount(1, $projects);
    }


    public function testTagManaging()
    {
        $id = $this->insertProject();
        $arr = [
            'version'   => 'v1'
        ];
        $arr2 = [
            'version'   => 'v2'
        ];


        $idTag = $this->bugmanager->saveTag($arr['version'], $id);
        $tags = $this->bugmanager->getAllTagsFromProject($id);
        $this->assertEquals($arr['version'], $tags[0]['version']);

        // Updating tag

        $this->bugmanager->saveTag($arr2['version'], $id, $idTag);
        $tag = $this->bugmanager->getTag($idTag);
        $this->assertEquals($arr2['version'], $tag['version']);

        // Setting status

        $this->bugmanager->setTagStatus($idTag, 'released');
        $tag = $this->bugmanager->getTag($idTag);
        $this->assertEquals($tag['status'], 'released');

        // Removing tag

        $result = $this->bugmanager->deleteTag($idTag);
        $this->assertTrue($result);
        $tags = $this->bugmanager->getAllTagsFromProject($id);
        $this->assertCount(0, $tags);

    }


    public function testIssueManaging()
    {
        $id = $this->insertProject();
        $arr = [
            'description' => 'Issue description'
        ];
        $arr2 = [
            'description' => 'Issue description updated'
        ];

        $idIssue = $this->bugmanager->saveIssue($arr, $id);
        $issues = $this->bugmanager->getAllIssuesFromProject($id);
        $this->assertEquals($arr['description'], $issues[0]['description']);

        //  Updating issue

        $this->bugmanager->saveIssue($arr2, $id, $idIssue);
        $issue = $issues = $this->bugmanager->getIssue($idIssue);
        $this->assertEquals($arr2['description'], $issue['description']);

        // Setting status

        $this->bugmanager->setIssuesStatus($idIssue, 'closed');
        $issue = $issues = $this->bugmanager->getIssue($idIssue);
        $this->assertEquals($issue['status'], 'closed');

        //  Removing issue

        $result = $this->bugmanager->deleteIssue($idIssue);
        $this->assertTrue($result);
        $issues = $this->bugmanager->getAllIssuesFromProject($id);
        $this->assertCount(0, $issues);

    }

}
