<?php namespace Helper;

use Input\Form;

class Validation
{
	public $input = null;
	public $errors = array();

	public function __construct(Form $input)
	{
		$this->input = $input;
	}

	public function isValid()
	{
		# hardcoded fields for validtion
		if (($dbcs_street = $this->input->getValue('dcbs_street')) === false) {
			$this->errors[] = "Street is required, please enter this value.";
		}

		if (($dbcs_street = $this->input->getValue('dcbs_city')) === false) {
			$this->errors[] = "City is required, please enter this value.";
		}

		if (($dbcs_street = $this->input->getValue('dcbs_state')) === false) {
			$this->errors[] = "State is required, please enter this value.";
		}

		if (($dbcs_street = $this->input->getValue('dcbs_zipcode')) === false) {
			$this->errors[] = "Zipcode is required, please enter this value.";
		}
		return empty($this->errors);
	}

	public function errors()
	{
		return $this->errors;
	}
}