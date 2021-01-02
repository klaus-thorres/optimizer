<?php

class Model
{
    private $files = array(
        'hardware' => 'data_hardware.csv',
        'van' => 'data_van.php'
    );

    public function checkFiles()
    {
        foreach (array_values($this->files) as $file) {
            if (!file_exists($file)) {
                return false;
            }
        }
        return true;
    }

    public function getHardware()
    {
        $lines = file($this->files['hardware']);
        foreach($lines as $line) {
            $data[] = str_getcsv($line, ';');
        }
        return $data;
    }

    public function getVan()
    {
        include $this->files['van'];
        if (!isset($capacity_max) or !isset($capacity_driver)) {
            return false;
        }
        return array($capacity_max, $capacity_driver);
    }
}
