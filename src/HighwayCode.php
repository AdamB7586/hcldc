<?php
/**
 * A PHP class to create elements for the Highway Code. Required the Highway Code database from the DVSA.
 *
 * @author Adam Binnersley
 * @copyright (c) 2017, Adam Binnersley
 * @license https://github.com/AdamB7586/hcldc/blob/master/LICENSE MIT
 * @package HighwayCode
 */
namespace DVSA;

use DBAL\Database;
use Configuration\Config;

class HighwayCode
{
    protected $db;
    protected $config;
    
    public $imagePath = '/images/highway-code/';
    private $rootPath;
    
    protected $rulesTable;
    
    /**
     * Constructor sets the essential variables needed to get the class to work
     * @param Database $db This should be an instance of the Database class
     * @param Config $config This should be and instance of the configuration class
     * @param string $rootPath This should be the server root path to get image information (do not include URL path)
     */
    public function __construct(Database $db, Config $config, $rootPath = '')
    {
        $this->db = $db;
        $this->config = $config;
        $this->setRootPath($rootPath);
    }
        
    /**
     * Returns the rules table name
     * @return string The name of the HC rules table will be returned
     */
    public function getRulesTable()
    {
        return $this->rulesTable;
    }
    
    /**
     * Sets the folder where the images can be found this should be the URL path relative to the web root e.g '/images/highway-code/'
     * @param string $path This should be the path of the highway code images folder
     * @return $this HighwayCode
     */
    public function setImagePath($path)
    {
        if (is_string($path)) {
            $this->imagePath = $path;
        }
        return $this;
    }
    
    /**
     * Returns the set path for the highway code images folder
     * @return string Location will be returned
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }
    
    /**
     * Set the server root path of the images not including the image path
     * @param string $path This should be the server root path e.g. $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPERATOR.'includes'.DIRECTORY_SEPERATOR.'hcimages'
     * @return $this HighwayCode
     */
    public function setRootPath($path)
    {
        if (is_string($path) && is_dir($path)) {
            $this->rootPath = $path;
        }
        return $this;
    }
    
    /**
     * The root path that has been set will be returned
     * @return string This should be the server root path of the images
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * Returns the rule information as an array
     * @param int|array $rule Should be either the rule number or numbers as an array
     * @return array
     */
    public function getRule($rule)
    {
        if (is_array($rule)) {
            $sql = array();
            $values = array();
            foreach ($rule as $ruleid) {
                $sql[] = "`hcno` = ?";
                $values[] = (int)$ruleid;
            }
            return $this->db->query("SELECT * FROM `{$this->config->table_hc_rules}` WHERE ".implode(' OR ', $sql)." ORDER BY `hcno` ASC;", $values);
        }
        return $this->db->select($this->config->table_hc_rules, ['hcno' => $rule]);
    }
    
    /**
     * Checks to see if this is the first section
     * @param int $section The current section number
     * @return boolean Returns true if it's the first section else returns false
     */
    public function isFirstSection($section)
    {
        return $this->isLastSection($section, '<');
    }
    
    /**
     * Checks to see if this is the last section
     * @param int $section The current section number
     * @param string $dir Should be set to either '>' or '<' depedning on which direction you are checking if its the last
     * @return boolean Returns true if it's the last section else returns false
     */
    public function isLastSection($section, $dir = '>')
    {
        if ($this->getSectionName([$dir, $section]) === false) {
            return true;
        }
        return false;
    }
    
    /**
     * Returns the Highway code Section name
     * @param int|array $section This should be the section number or an array for section less or greater than
     * @return string|false If the section exists the name will be returned else will return false
     */
    public function getSectionName($section)
    {
        $title = $this->db->select($this->config->table_hc_sections, ['sec_no' => $section], ['title']);
        if (!empty($title)) {
            return $title['title'];
        }
        return false;
    }

    /**
     * Returns the entire section HTML code
     * @param int $section The section number you wish to return
     * @return string Returns the section HTML code
     */
    public function getSectionRules($section)
    {
        if (is_numeric($section)) {
            $rules = $this->db->selectAll($this->config->table_hc_rules, ['pubsec' => $section], ['hcno', 'hcrule', 'hctitle', 'imagetitle1', 'imagetitle2', 'imagefooter1'], ['hcno' => 'ASC']);
            if (is_array($rules)) {
                foreach ($rules as $i => $rule) {
                    if ($rule['imagetitle1']) {
                        $rules[$i]['image'] = $this->buildImage($rule['imagetitle1']);
                    }
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
    public function buildSection($section)
    {
        if (is_numeric($section)) {
            $hc = [];
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
    public function listSections()
    {
        return $this->db->selectAll($this->config->table_hc_sections);
    }
    
    /**
     * Build the image information for a highway code image
     * @param string $image Should bet the image name
     * @return string|false returns the image HTML code with with and height
     */
    public function buildImage($image)
    {
        if (!is_null($image)) {
            if (file_exists($this->getRootPath().$this->getImagePath().$image)) {
                $img = [];
                list($width, $height) = getimagesize($this->getRootPath().$this->getImagePath().$image);
                $img['image'] = $this->getImagePath().$image;
                $img['width'] = $width;
                $img['height'] = $height;
                return $img;
            }
        }
        return false;
    }
}
