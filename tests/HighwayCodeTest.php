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
            $this->hc = new HighwayCode($this->db);
        }
    }
    
    protected function tearDown() {
        $this->hc = null;
    }
    
    public function testExample() {
        $this->markTestIncomplete();
    }
}
