<?php

namespace Aliyun\Test\Core\Auth;
use Aliyun\Core\Auth\Credential;
use PHPUnit\Framework\TestCase;
use Aliyun\Core\Config;

class CredentialTest extends TestCase
{
    public function setUp() {
        Config::load();
    }

	public function testCredential()
	{
		$credential = new Credential(\Illuminate\Support\Facades\Config::get('sms.AccessKeyId'), \Illuminate\Support\Facades\Config::get('sms.AccessKeySecret'));
		$this->assertEquals("accessKeyId",$credential->getAccessKeyId());
		$this->assertEquals("accessSecret",$credential->getAccessSecret());
		$this->assertNotNull($credential->getRefreshDate());
		
		$dateNow = date("Y-m-d\TH:i:s\Z");
		$credential->setExpiredDate(1);
		$this->assertNotNull($credential->getExpiredDate());
		$this->assertTrue($credential->getExpiredDate() > $dateNow);	
	}
	
	
}