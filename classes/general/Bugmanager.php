<?php

/**
 * @author Serkin Alexander <serkin.alexander@gmail.com>
 */
class Bugmanager
{
    /**
     * @var PDO
     */
    protected $dbh = null;

    /**
     * @var string
     */
    protected $dbDSN;

    /**
     * @var string
     */
    protected $dbUser;

    /**
     * @var string
     */
    protected $dbPassword;

    /**
     * @param string $dbDSN
     * @param string $dbUser
     * @param string $dbPassword
     */
    public function __construct($dbDSN, $dbUser, $dbPassword = '')
    {
        $this->dbDSN = $dbDSN;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
    }

    /**
     * Connects to database.
     *
     * @throws PDOException
     */
    public function connect()
    {
        $dbh = new PDO($this->dbDSN, $this->dbUser, $this->dbPassword);
        $this->dbh = $dbh;
        $this->dbh->exec('SET NAMES utf8');
    }

    public function getError()
    {
        return $this->dbh->errorInfo();
    }

    /**
     * Gets all projects.
     *
     * @return array
     */
    public function getAllProjects()
    {
        $returnValue = [];

        $sth = $this->dbh->prepare('SELECT * FROM `project`');
        $sth->execute();

        foreach ($sth->fetchAll(PDO::FETCH_ASSOC) as $project) {
            $project['amount_issues'] = $this->countIssuesInProject($project['id_project']);
            $returnValue[] = $project;
        }

        return $returnValue;
    }

    public function getAllIssuesFromProject($idProject, $status = 'open')
    {
        $sth = $this->dbh->prepare("SELECT * FROM `issue` WHERE `id_project` = ? and `status` = ?");
        $sth->bindParam(1, $idProject,  PDO::PARAM_INT);
        $sth->bindParam(2, $status,     PDO::PARAM_STR);
        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Counts open issues in project.
     *
     * @param int $idProject
     *
     * @return int
     */
    public function countIssuesInProject($idProject, $status = 'open')
    {
        $sth = $this->dbh->prepare('SELECT COUNT(*) AS TOTAL FROM `issue` WHERE `id_project` = ? and `status` = ?');
        $sth->bindParam(1, $idProject,  PDO::PARAM_INT);
        $sth->bindParam(2, $status,     PDO::PARAM_STR);
        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC)['TOTAL'];
    }

    public function getAllUsers()
    {
        $sth = $this->dbh->prepare('SELECT * FROM `user`');

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTagsFromProject($idProject, $status = 'open')
    {
        $sth = $this->dbh->prepare('SELECT * FROM `tag` WHERE `id_project` = ? and `status` = ?');
        $sth->bindParam(1, $idProject,  PDO::PARAM_INT);
        $sth->bindParam(2, $status,     PDO::PARAM_STR);

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIssue($idIssue)
    {
        $sth = $this->dbh->prepare('SELECT * FROM `issue` WHERE `id_issue` = ?');
        $sth->bindParam(1, $idIssue, PDO::PARAM_INT);

        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    public function getTag($idTag)
    {
        $sth = $this->dbh->prepare('SELECT * FROM `tag` WHERE `id_tag` = ?');
        $sth->bindParam(1, $idTag, PDO::PARAM_INT);

        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all information about project.
     *
     * @param int $idProject
     *
     * @return array
     */
    public function getProjectById($idProject)
    {
        $sth = $this->dbh->prepare('SELECT * FROM `project` WHERE `id_project` = ?');
        $sth->bindParam(1, $idProject, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Saves project.
     *
     * Edits project if $idProject was specified
     *
     * @param array $arr
     * @param int   $idProject
     *
     * @return int|bool False on error
     */
    public function saveProject($arr, $idProject = null)
    {
        if (is_null($idProject)) {
            $sth = $this->dbh->prepare('INSERT INTO `project` (`name`) VALUES(?)');
        } else {
            $sth = $this->dbh->prepare('UPDATE `project` SET `name` = ? WHERE `id_project` = ?');
        }

        $sth->bindParam(1, $arr['name'], PDO::PARAM_STR);

        if (is_null($idProject)) {
            $sth->execute();
            $returnValue = $this->dbh->lastInsertId() ? $this->dbh->lastInsertId() : 0;
        } else {
            $sth->bindParam(2, $idProject, PDO::PARAM_INT);
            $sth->execute();
            $returnValue = $idProject;
        }

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
     * Removes project.
     *
     * @param int $idProject
     *
     * @return bool
     */
    public function deleteProject($idProject)
    {
        $sth = $this->dbh->prepare('DELETE FROM `project` WHERE `id_project` = ?');

        $sth->bindParam(1, $idProject, PDO::PARAM_INT);

        return $sth->execute();
    }

    public function saveTag($version, $idProject, $idTag = null)
    {
        if (is_null($idTag)) {
            $sth = $this->dbh->prepare('INSERT INTO `tag` (`version`, `id_project`) VALUES(?, ?)');
        } else {
            $sth = $this->dbh->prepare('UPDATE `tag` SET `version` = ? WHERE `id_tag` = ?');
        }

        $sth->bindParam(1, $version, PDO::PARAM_STR);

        if (is_null($idTag)) {
            $sth->bindParam(2, $idProject, PDO::PARAM_INT);
            $sth->execute();
            $returnValue = $this->dbh->lastInsertId() ? $this->dbh->lastInsertId() : 0;
        } else {
            $sth->bindParam(2, $idTag, PDO::PARAM_INT);
            $sth->execute();
            $returnValue = $idTag;
        }

        return $returnValue;
    }
    /**
     * Saves issue
     *
     * @param array  $arr
     * @param int    $idIssue If is not null edit record else insert new
     *
     * @return bool
     */
    public function saveIssue($arr, $idIssue = null)
    {
        $arr['id_project'] = !empty($arr['id_project'])  ? $arr['id_project']    : null;
        $arr['id_tag'] = !empty($arr['id_tag'])      ? $arr['id_tag']        : null;
        $arr['description'] = !empty($arr['description']) ? $arr['description']   : null;
        $arr['type'] = !empty($arr['type'])        ? $arr['type']          : null;

        return is_null($idIssue) ? $this->insertIssue($arr) : $this->updateIssue($arr, $idIssue);
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
     * @param int $idIssue
     *
     * @return bool
     */
    public function deleteIssue($idIssue)
    {
        $sth = $this->dbh->prepare('DELETE FROM `issue` WHERE `id_issue` = ?');

        $sth->bindParam(1, $idIssue, PDO::PARAM_INT);

        return $sth->execute();
    }

    public function deleteTag($idTag)
    {
        $sth = $this->dbh->prepare('DELETE FROM `tag` WHERE `id_tag` = ?');
        $sth->bindParam(1, $idTag, PDO::PARAM_INT);

        return $sth->execute();
    }
}
