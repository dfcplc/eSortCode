<?php
 
use Dfcplc\eSortCode\eSortCode;
use PHPUnit\Framework\TestCase;
 
class AccountNumberTest extends TestCase
{
	public function testInvalidAccountNumberLetters() {
		$this->assertFalse(eSortCode::validate_account_number('ABC12312'));
	}

	public function testInvalidAccountNumberTooLong() {
		$this->assertFalse(eSortCode::validate_account_number('123456789'));
	}

	public function testInvalidAccountNumberTooShort() {
		$this->assertFalse(eSortCode::validate_account_number('1234567'));
	}

	public function testValidAccountNumber() {
		$this->assertTrue(eSortCode::validate_account_number('12345678'));
	}

	public function testValidAccountNumberZeros() {
		$this->assertTrue(eSortCode::validate_account_number('00123456'));
	}
}