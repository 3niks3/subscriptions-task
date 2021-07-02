<?php


namespace App\Core;


class Validator
{
    public $fields;
    public $rules;
    public $messages;

    public $failed = false;
    public $error_messages = [];
    public $error_rules = [];


    public function __construct($fields, $rules, $messages)
    {
        $this->fields = $fields;
        $this->rules = $rules;
        $this->messages = $messages;
    }

    public function validate()
    {
        foreach($this->fields as $field => $value) {
            $rules = $this->rules[$field] ?? [];

            foreach($rules as $rule) {
                $rule_name = $rule.'Rule';
                if(!method_exists($this, $rule_name)) {
                    continue;
                }

                $result =  call_user_func_array([$this,$rule_name],[$value]);

                if($result == false) {
                    $this->failed = true;
                    $this->error_rules[$field] = $this->error_rules[$field]??[];
                    array_push($this->error_rules[$field], $rule);

                    $message = $this->messages[$field][$rule] ?? '';
                    $this->error_messages[$field][$rule] = $message;
                }

            }
        }
    }

    public function failed()
    {
        return $this->failed;
    }

    public function getMessages($field = null)
    {
        return ($field) ? ($this->error_messages[$field] ?? []):$this->error_messages;
    }

    public function getFirstMessages($field = null)
    {
        $messages = $this->getMessages();
        $new_message = [];

        foreach($messages as $field_name => $messageBag)
        {
            $new_message[$field_name] = array_shift($messageBag);
        }

        $new_message = ($field) ? ($new_message[$field] ?? []):$new_message;

        return $new_message;
    }

    public function gerErrorRules()
    {

    }

    public function setMessage($field, $rule, $message)
    {
        $this->error_messages[$field][$rule] = $message;
    }


    /*
     * Rules
     */

    private function requiredRule($value)
    {
        return (!empty($value)) ? true : false;
    }

    private function emailRule($value)
    {
        return (filter_var($value, FILTER_VALIDATE_EMAIL)) ? true : false;
    }

    private function excludeColombiaRule($value)
    {
        $email_location_extension = explode('.',$value);
        $email_location_extension = end($email_location_extension);

        return ($email_location_extension == 'co') ? false : true;
    }
}