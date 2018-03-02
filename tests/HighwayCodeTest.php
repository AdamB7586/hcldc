<?php

namespace DVSA\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use DVSA\HighwayCode;

class HighwayCodeTest extends TestCase {
    protected $db;
    protected $hc;
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::setRootPath
     * @covers DVSA\HighwayCode::setAudioStatus
     * @covers DVSA\HighwayCode::setAudioPath
     */
    protected function setUp() {
        $this->db = new Database($GLOBALS['HOSTNAME'], $GLOBALS['USERNAME'], $GLOBALS['PASSWORD'], $GLOBALS['DATABASE']);
        if(!$this->db->isConnected()){
            $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        else{
            $this->db->query(file_get_contents(dirname(dirname(__FILE__)).'/database/mysql_database.sql'));
            $this->db->query(file_get_contents(dirname(__FILE__).'/sample_data/mysql_data.sql'));
            $this->hc = new HighwayCode($this->db, dirname(__FILE__).'/sample_data', '/audio', true);
        }
    }
    
    protected function tearDown() {
        $this->hc = null;
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::setRootPath
     * @covers DVSA\HighwayCode::getRootPath
     */
    public function testSetPath() {
        $this->assertEquals(dirname(__FILE__).'/sample_data', $this->hc->getRootPath());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setRootPath(dirname(__FILE__).'/sample_data/some-random-dir'));
        $this->assertEquals(dirname(__FILE__).'/sample_data/some-random-dir', $this->hc->getRootPath());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setRootPath(1548451));
        $this->assertEquals(dirname(__FILE__).'/sample_data/some-random-dir', $this->hc->getRootPath());
        $this->hc->setRootPath(dirname(__FILE__).'/sample_data');
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::setImagePath
     * @covers DVSA\HighwayCode::getImagePath
     */
    public function testSetImagePath() {
        $this->assertEquals('/images/highway-code/', $this->hc->getImagePath());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setImagePath(356345345));
        $this->assertEquals('/images/highway-code/', $this->hc->getImagePath());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setImagePath(true));
        $this->assertEquals('/images/highway-code/', $this->hc->getImagePath());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setImagePath('/images/'));
        $this->assertEquals('/images/', $this->hc->getImagePath());
        $this->hc->setImagePath('/images/highway-code/');
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::setRulesTable
     * @covers DVSA\HighwayCode::getRulesTable
     * @covers DVSA\HighwayCode::setSectionTable
     * @covers DVSA\HighwayCode::getSectionTable
     */
    public function testSetTables() {
        $this->assertEquals('highway_code', $this->hc->getRulesTable());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setRulesTable(356345345));
        $this->assertEquals('highway_code', $this->hc->getRulesTable());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setRulesTable('my_rules_table'));
        $this->assertEquals('my_rules_table', $this->hc->getRulesTable());
        

        $this->assertEquals('highway_code_section', $this->hc->getSectionTable());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setSectionTable(false));
        $this->assertEquals('highway_code_section', $this->hc->getSectionTable());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setSectionTable('my_section_table'));
        $this->assertEquals('my_section_table', $this->hc->getSectionTable());
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::setAudioPath
     * @covers DVSA\HighwayCode::getAudioPath
     */
    public function testAudioPath() {
        $this->assertEquals('/audio', $this->hc->getAudioPath());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setAudioPath(false));
        $this->assertEquals('/audio', $this->hc->getAudioPath());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setAudioPath(''));
        $this->assertEquals('', $this->hc->getAudioPath());
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::setAudioStatus
     * @covers DVSA\HighwayCode::getAudioStatus
     */
    public function testAudioStatus() {
        $this->assertFalse($this->hc->getAudioStatus());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setAudioStatus(true));
        $this->assertTrue($this->hc->getAudioStatus());
        $this->assertObjectHasAttribute('audioEnabled', $this->hc->setAudioStatus('incorrect_value'));
        $this->assertTrue($this->hc->getAudioStatus());
        $this->hc->setAudioStatus(false);
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::getRule
     * @covers DVSA\HighwayCode::getRulesTable
     */
    public function testGetRule(){
        $this->assertFalse($this->hc->getRule(false));
        $this->assertFalse($this->hc->getRule('hello-world'));
        $this->assertFalse($this->hc->getRule(-1));
        $this->assertNotFalse($this->hc->getRule(46));
        $this->assertArrayHasKey('hcrule', $this->hc->getRule(46));
        $this->assertContains('Rule 46', $this->hc->getRule(46)['hcrule']);
        $this->assertArrayHasKey('hcrule', $this->hc->getRule(array(1, 2))[1]);
        $this->assertContains('Rule 2', $this->hc->getRule(array(1, 2))[1]['hcrule']);
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::getSectionName
     * @covers DVSA\HighwayCode::getSectionTable
     */
    public function testGetSectionName(){
        $this->assertFalse($this->hc->getSectionName(-1));
        $this->assertFalse($this->hc->getSectionName('hello-world'));
        $this->assertEquals('Rules for pedestrians', $this->hc->getSectionName(1));
        $this->assertEquals('Motorways', $this->hc->getSectionName(12));
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::getSectionRules
     * @covers DVSA\HighwayCode::getRulesTable
     * @covers DVSA\HighwayCode::buildImage
     * @covers DVSA\HighwayCode::getRootPath
     * @covers DVSA\HighwayCode::getImagePath
     * @covers DVSA\HighwayCode::getAudioStatus
     * @covers DVSA\HighwayCode::addAudio
     */
    public function testGetSectionRules(){
        $this->assertFalse($this->hc->getSectionRules('test'));
        $this->assertArrayHasKey('hcrule', $this->hc->getSectionRules(2)[7]);
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::buildSection
     * @covers DVSA\HighwayCode::getSectionName
     * @covers DVSA\HighwayCode::getSectionTable
     * @covers DVSA\HighwayCode::getSectionRules
     * @covers DVSA\HighwayCode::getRulesTable
     * @covers DVSA\HighwayCode::buildImage
     * @covers DVSA\HighwayCode::getRootPath
     * @covers DVSA\HighwayCode::getImagePath
     * @covers DVSA\HighwayCode::getAudioStatus
     * @covers DVSA\HighwayCode::addAudio
     * @covers DVSA\HighwayCode::isFirstSection
     * @covers DVSA\HighwayCode::isLastSection
     */
    public function testBuildSection(){
        $this->assertEquals('Rules for pedestrians', $this->hc->buildSection(1)['title']);
        $this->assertArrayHasKey('hcrule', $this->hc->buildSection(1)['rules'][0]);
        $this->assertContains('Rule 2', $this->hc->buildSection(1)['rules'][1]['hcrule']);
        $this->assertTrue($this->hc->buildSection(1)['isFirst']);
        $this->assertFalse($this->hc->buildSection(1)['isLast']);
        $this->assertFalse($this->hc->buildSection(30)['isFirst']);
        $this->assertTrue($this->hc->buildSection(30)['isLast']);
        $this->assertFalse($this->hc->buildSection('beer'));
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::listSections
     * @covers DVSA\HighwayCode::getSectionTable
     */
    public function testListSections(){
        $this->assertArrayHasKey('title', $this->hc->listSections()[3]);
        $this->assertContains('Rules for ', $this->hc->listSections()[3]['title']);
    }
}
