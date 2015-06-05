<?php


class BugmanagerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testInsertingProject()
    {

        require dirname(dirname(__DIR__)) . '/classes/general/Bugmanager.php';

        $arr = [
            'name'      => 'project 1'
        ];

        $bugmanager = new Bugmanager($GLOBALS['db_dsn'], $GLOBALS['db_user'], $GLOBALS['db_password']);
        $bugmanager->connect();
        $id = $bugmanager->saveProject($arr['name']);
        $project = $bugmanager->getProjectById($id);

        $this->assertTrue(is_numeric($id));
        $this->assertEquals($project['name'], $arr['name']);


        // Test update project

    }
}
