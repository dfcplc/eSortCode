<?php namespace Dfcplc\eSortCode;
 
class eSortCode
{

	private static $ws_url = 'https://ws.esortcode.com/bankdetails.asmx?WSDL';
	private static $ws_trace = false;
	private static $ws_exception = true;
	private static $ws_cache_wsdl = WSDL_CACHE_NONE;

	public static function validate_bank_details($username, $guid, $sortcode, $account_number, $ip_address = '') {

		$return = new \stdClass;
		$return->valid = true;
		$return->error_message = '';
		$return->log_message = null;
		$return->branch_details = null;
		$return->response = null;

		if(self::validate_sortcode($sortcode) === false) {
			$return->valid = false;
			$return->error_message = 'Sort Code is Invalid';
			return $return;
		}

		if(self::validate_account_number($account_number) === false) {
			$return->valid = false;
			$return->error_message = 'Account Number is Invalid';
			return $return;
		}

		$params = array(
			'sSortcode' => $sortcode,
			'sAccountNumber' => $account_number,
			'sUserName' => $username,
			'sGUID' => $guid,
			'sIPAddress' => $ip_address
		);

		try {

			$client = new \SoapClient(self::$ws_url, array(
				'trace' => self::$ws_trace,
				'exception' => self::$ws_exception,
				'cache_wsdl' => self::$ws_cache_wsdl
			));

			$response = $client->ValidateAccountGetBranchDetails($params);

			if($response === false || empty($response) || !isset($response->ValidateAccountGetBranchDetailsResult)) {
				$return->valid = true;
				$return->error_message = '';
				$return->log_message = 'Invalid Return';
				$return->response = $response;
				return $return;
			}

			if(isset($response->ValidateAccountGetBranchDetailsResult->ValidationMessage) && stripos($response->ValidateAccountGetBranchDetailsResult->ValidationMessage, 'zero credits')!==false) {
				$return->valid = true;
				$return->error_message = '';
				$return->log_message = $response->ValidateAccountGetBranchDetailsResult->ValidationMessage;
				$return->response = $response;
				return $return;
			}

			if(isset($response->ValidateAccountGetBranchDetailsResult->IsError) && $response->ValidateAccountGetBranchDetailsResult->IsError === true) {
				$return->valid = false;
				$return->error_message = $response->ValidateAccountGetBranchDetailsResult->ErrorMessage;
				$return->log_message = '';
				$return->response = $response;
				return $return;
			}

			if(isset($response->ValidateAccountGetBranchDetailsResult->ValidationMessage) && $response->ValidateAccountGetBranchDetailsResult->ValidationMessage !== "VALID") {
				$return->valid = false;
				$return->error_message = trim(str_ireplace('INVALID -', '', $response->ValidateAccountGetBranchDetailsResult->ValidationMessage));
				$return->log_message = '';
				$return->response = $response;
				return $return;
			}

			if(isset($response->ValidateAccountGetBranchDetailsResult->BACSTransactionsDisallowedDR) && $response->ValidateAccountGetBranchDetailsResult->BACSTransactionsDisallowedDR === 'DR') {
				$return->valid = false;
				$return->error_message = 'Account does not accept Direct Debits';
				$return->log_message = '';
				$return->response = $response;
				return $return;
			}

			if(isset($response->ValidateAccountGetBranchDetailsResult->BACSTransactionsDisallowedAU) && $response->ValidateAccountGetBranchDetailsResult->BACSTransactionsDisallowedAU === 'AU') {
				$return->valid = false;
				$return->error_message = 'Account does not accept DD Instructions';
				$return->log_message = '';
				$return->response = $response;
				return $return;
			}

			$branch_details = new \stdClass();
			$branch_details->branch = trim((string) $response->ValidateAccountGetBranchDetailsResult->GENERALShortBranchTitle);
			$branch_details->bank_name = trim((string) $response->ValidateAccountGetBranchDetailsResult->GENERALShortNameOwningBank);
			$branch_details->address1 = trim((string) $response->ValidateAccountGetBranchDetailsResult->PRINTAddressLine1);
			$branch_details->address2 = trim((string) $response->ValidateAccountGetBranchDetailsResult->PRINTAddressLine2);
			$branch_details->address3 = trim((string) $response->ValidateAccountGetBranchDetailsResult->PRINTAddressLine3);
			$branch_details->address4 = trim((string) $response->ValidateAccountGetBranchDetailsResult->PRINTAddressLine4);
			$branch_details->town = trim((string) $response->ValidateAccountGetBranchDetailsResult->PRINTTown);
			$branch_details->postcode = trim(trim((string) $response->ValidateAccountGetBranchDetailsResult->PRINTPostcodeField1)." ".trim((string) $response->ValidateAccountGetBranchDetailsResult->PRINTPostcodeField2));

			$return->valid = true;
			$return->error_message = '';
			$return->log_message = '';
			$return->response = $response;
			$return->branch_details = $branch_details;
			return $return;

		} catch(\Exception $e) {
			$return->valid = true;
			$return->error_message = '';
			$return->log_message = $e->getMessage();
			$return->response = $e;
			return $return;
		}
	}

	public static function validate_sortcode($sortcode) {
		if(preg_match('/^[0-9]{6}$/', $sortcode)) {
			return true;
		}

		return false;
	}


	public static function validate_account_number($account_number) {
		if(preg_match('/^[0-9]{8}$/', $account_number)) {
			return true;
		}

		return false;
	}
}