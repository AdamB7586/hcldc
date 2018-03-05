<?php
/**
 * A PHP class to create elements for the Highway Code. Required the Highway Code database from the DVSA.
 *
 * @author Adam Binnersley
 * @copyright (c) 2017, Adam Binnersley
 * @license https://github.com/AdamB7586/hcldc/blob/master/LICENSE MIT
 * @package HighwayCode
 * @version v1.0.0
 */
namespace DVSA;

use DBAL\Database;

class HighwayCode{
    protected $db;
    
    public $audioEnabled = true;
    
    protected $rulesTable = 'highway_code';
    protected $sectionTable = 'highway_code_section';
    
    public $imagePath = '/images/highway-code/';
    private $rootPath;
    private $audioPath;
    
    /**
     * Constructor sets the essential variables needed to get the class to work  
     * @param Database $db This should be an instance of the Database class
     * @param string $rootPath This should be the server root path to get image information (do not include URL path)
     * @param string $audioPath This should be the path to the audio files the MP3 and OGG files will be included in the returned HTML automatically
     * @param boolean $audio If you don't want the audio file HTML to be returned set to false else set to true (default = true)
     */
    public function __construct(Database $db, $rootPath = '', $audioPath = '', $audio = true){
        $this->db = $db;
        $this->setRootPath($rootPath)
             ->setAudioStatus($audio)
             ->setAudioPath($audioPath);
    }
    
    /**
     * Set the Rules table parameter
     * @param string $table This should be the name of the rules table in the database
     * @return $this HighwayCode
     */
    public function setRulesTable($table = 'highway_code'){
        if(is_string($table)){
            $this->rulesTable = $table;
        }
        return $this;
    }
    
    /**
     * Returns the rules table name
     * @return string The name of the HC rules table will be returned
     */
    public function getRulesTable(){
        return $this->rulesTable;
    }
    
    /**
     * Sets the section table parameter
     * @param string $table This should be the section table in the database
     * @return $this HighwayCode
     */
    public function setSectionTable($table = 'highway_code_section'){
        if(is_string($table)){
            $this->sectionTable = $table;
        }
        return $this;
    }
    
    /**
     * Returns the section table name
     * @return string The name of the HC section table will be returned
     */
    public function getSectionTable(){
        return $this->sectionTable;
    }
    
    /**
     * Sets the audio status if it's set to true audio will be returned as part of the rules
     * @param boolean $audio If set to true audio HTML will returned else nothing will be returned
     * @return $this HighwayCode
     */
    public function setAudioStatus($audio){
        if(is_bool($audio)){
            $this->audioEnabled = $audio;
        }
        return $this;
    }
    
    /**
     * Returns the current status of the audio (true or false)
     * @return boolean Returns true if audio should be returned else will return false
     */
    public function getAudioStatus(){
        return (bool)$this->audioEnabled;
    }
    
    /**
     * Sets the audio path which should be inserted into the HTML code to be rendered (URL Path)
     * @param string $path Sets the location where the audio files can be found
     * @return $this HighwayCode
     */
    public function setAudioPath($path){
        if(is_string($path)){
            $this->audioPath = $path;
        }
        return $this;
    }
    
    /**
     * Returns the location where the audio files can be found
     * @return string This will be the URL of the main folder where the highway code audio can be found (the MP3 and OGG folders will be added in the HTML)
     */
    public function getAudioPath(){
        return $this->audioPath;
    }
    
    /**
     * Sets the folder where the images can be found this should be the URL path relative to the web root e.g '/images/highway-code/' 
     * @param string $path This should be the path of the highway code images folder
     * @return $this HighwayCode
     */
    public function setImagePath($path){
        if(is_string($path)){
            $this->imagePath = $path;
        }
        return $this;
    }
    
    /**
     * Returns the set path for the highway code images folder
     * @return string Location will be returned
     */
    public function getImagePath(){
        return $this->imagePath;
    }
    
    /**
     * Set the server root path of the images not including the image path
     * @param string $path This should be the server root path e.g. $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPERATOR.'includes'.DIRECTORY_SEPERATOR.'hcimages'
     * @return $this HighwayCode
     */
    public function setRootPath($path){
        if(is_string($path) && is_dir($path)){
            $this->rootPath = $path;
        }
        return $this;
    }
    
    /**
     * The root path that has been set will be returned
     * @return string This should be the server root path of the images
     */
    public function getRootPath(){
        return $this->rootPath;
    }

    /**
     * Returns the rule information as an array
     * @param int|array $rule Should be either the rule number or numbers as an array
     * @return array 
     */
    public function getRule($rule){
        if(is_array($rule)){
            $sql = array();
            $values = array();
            foreach($rule as $ruleid){
                $sql[] = "`hcno` = ?";
                $values[] = (int)$ruleid;
            }
            return $this->db->query("SELECT * FROM `".$this->getRulesTable()."` WHERE ".implode(' OR ', $sql)." ORDER BY `hcno` ASC;", $values);
        }
        return $this->db->select($this->getRulesTable(), array('hcno' => $rule));
    }
    
    /**
     * Checks to see if this is the first section
     * @param int $section The current section number
     * @return boolean Returns true if it's the first section else returns false
     */
    public function isFirstSection($section){
        if($this->getSectionName(array('<', $section)) === false){return true;}
        return false;
    }
    
    /**
     * Checks to see if this is the last section
     * @param int $section The current section number
     * @return boolean Returns true if it's the last section else returns false
     */
    public function isLastSection($section){
        if($this->getSectionName(array('>', $section)) === false){return true;}
        return false;
    }
    
    /**
     * Returns the Highway code Section name
     * @param int|array $section This should be the section number or an array for section less or greater than
     * @return string|false If the section exists the name will be returned else will return false
     */
    public function getSectionName($section){
        $title = $this->db->select($this->getSectionTable(), array('sec_no' => $section), array('title'));
        if(!empty($title)){
            return $title['title'];
        }
        return false;
    }

    /**
     * Returns the entire section HTML code
     * @param int $section  The section number you wish to return
     * @return string Returns the section HTML code
     */
    public function getSectionRules($section){
        if(is_numeric($section)){
            $rules = $this->db->selectAll($this->getRulesTable(), array('pubsec' => (intval($section) + 1)), array('hcno', 'hcrule', 'hctitle', 'imagetitle1', 'imagetitle2', 'imagefooter1'), array('hcno' => 'ASC'));
            if(is_array($rules)){
                foreach($rules as $i => $rule){
                    if($rule['imagetitle1']){$rules[$i]['image'] = $this->buildImage($rule['imagetitle1']);}
                    if($this->getAudioStatus()){$rules[$i]['audio'] = $this->addAudio($rule['hcno']);}
                }
            }
            return $rules;
        }
        return false;
    }
    
    /**
     * Brings all of the required information together to build a section
     * @param int $section This should be the section number you wish to build
     * @return array|false An array containing all of the values required to build will be returned
     */
    public function buildSection($section){
        if(is_numeric($section)){
            $hc = array();
            $hc['title'] = $this->getSectionName($section);
            $hc['rules'] = $this->getSectionRules($section);
            $hc['isFirst'] = $this->isFirstSection($section);
            $hc['isLast'] = $this->isLastSection($section);
            return $hc;
        }
        return false;
    }
    
    /**
     * List all of the highway code sections
     * @return string Returns a list of link for the highway code sections
     */
    public function listSections(){
        return $this->db->selectAll($this->getSectionTable());
    }
    
    /**
     * Build the image information for a highway code image
     * @param string $image Should bet the image name
     * @return string|false returns the image HTML code with with and height
     */
    public function buildImage($image){
        if(!is_null($image)){
            if(file_exists($this->getRootPath().$this->getImagePath().$image)){
                $img = array();
                list($width, $height) = getimagesize($this->getRootPath().$this->getImagePath().$image);
                $img['image'] = $this->getImagePath().$image;
                $img['width'] = $width;
                $img['height'] = $height;
                return $img;
            }
        }
        return false;
    }
    
    /**
     * Returns the HTML5 audio HTML information as a string
     * @param int $prim This should be the question prim number
     * @return string Returns the HTML needed for the audio if audio enabled
     */
    protected function addAudio($prim){
        if($this->getAudioStatus() === true){
            return '<div class="sound" id="audioanswerhc'.$prim.'"><audio id="audiohc'.$prim.'" preload="auto"><source src="'.$this->getAudioPath().'mp3/hc'.$prim.'.mp3" type="audio/mpeg"><source src="'.$this->getAudioPath().'ogg/hc'.$prim.'.ogg" type="audio/ogg"></audio></div>';
        }
        return '';
    }
}