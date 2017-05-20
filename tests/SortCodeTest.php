<?php
 
use Dfcplc\eSortCode\eSortCode;
use PHPUnit\Framework\TestCase;
 
class SortCodeTest extends TestCase
{
	public function testInvalidSortCodeLetters() {
		$this->assertFalse(eSortCode::validate_sortcode('ABC123'));
	}

	public function testInvalidSortCodeTooLong() {
		$this->assertFalse(eSortCode::validate_sortcode('1111111'));
	}

	public function testInvalidSortCodeTooShort() {
		$this->assertFalse(eSortCode::validate_sortcode('11111'));
	}

	public function testValidSortCode() {
		$this->assertTrue(eSortCode::validate_sortcode('111111'));
	}

	public function testValidSortCodeZeros() {
		$this->assertTrue(eSortCode::validate_sortcode('000111'));
	}
}