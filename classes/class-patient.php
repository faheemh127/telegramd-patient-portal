<?php
if (! class_exists('hldPatient')) {

    class hldPatient
    {
        public $patient_name;

        // Constructor to set patient name
        public function __construct($patient_name)
        {
            $this->patient_name = $patient_name;
        }

        // Example method to return patient name
        public function get_patient_name()
        {
            return $this->patient_name;
        }
    }
}
// Create object of the class
$hldPatient = new hldPatient("John Doe");
