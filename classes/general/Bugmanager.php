<?php

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