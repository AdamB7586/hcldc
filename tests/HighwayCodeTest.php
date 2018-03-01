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
            $this->hc = new HighwayCode($this->db, dirname(__FILE__).'/sample_data', '/audio', false);
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
     * @covers DVSA\HighwayCode::setRootPath
     * @covers DVSA\HighwayCode::getRootPath
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
        $this->markTestIncomplete();
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::setAudioPath
     * @covers DVSA\HighwayCode::getAudioPath
     */
    public function testAudioPath() {
        $this->markTestIncomplete();
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
        $this->markTestIncomplete();
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::getSectionName
     * @covers DVSA\HighwayCode::getSectionTable
     */
    public function testGetSectionName(){
        $this->markTestIncomplete();
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
        $this->markTestIncomplete();
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
        $this->markTestIncomplete();
    }
    
    /**
     * @covers DVSA\HighwayCode::__construct
     * @covers DVSA\HighwayCode::listSections
     * @covers DVSA\HighwayCode::getSectionTable
     */
    public function testListSections(){
        $this->markTestIncomplete();
    }
}
