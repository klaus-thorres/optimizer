<?php

class Controller
{
    private $request = null;

    private $View = null;

    private $Model = null;

    private $equations_system = null;


    public function __construct($request)
    {
        $this->request = $request;
        $this->View = new View();
        $this->Model = new Model();

        if (!isset($request['display'])) {
            $this->request['display'] = 'default';
        }

        switch ($this->request['display']) {
            case 'check_data':
                $this->checkData();
                break;
            case 'output_result':
                $this->outputResult();
                break;
            case 'default':
                //nobreak
            default:
                $this->default();
                break;
        }

    }

    private function outputResult()
    {
        $hardware =  $this->Model->getHardware();
        $van =  $this->Model->getVan();

        $this->assembleEquationSystem($hardware, $van);
        $this->solveEquationSystem();
        $hw_list = $this->getHwList();
        $result = $this->extendHwArray($hw_list, $hardware, $van);

        $this->View->assign('hardware', $result['hardware']);
        $this->View->assign('van', $result['van']);
        $this->View->assign('equation_system', $this->equation_system);
        $this->View->setTemplate('output_result');
    }

    private function solveEquationSystem()
    {
        $solved = false;
        $pivot_column = $this->getPivotColumn();

        while ($solved == false) {

            $pivot_row = $this->getPivotRow($pivot_column);
            $this->clearPivotColumn($pivot_column, $pivot_row);
            $pivot_column = $this->getPivotColumn();

            if ($pivot_column == 'done') {
                $solved = true;
            }
        }
        $hw_list = $this->getHwList();
        return $hw_list;
    }

    private function getPivotColumn()
    {
        asort($this->equation_system['P']);
        $pivot_column = array_key_first($this->equation_system['P']);

        if ($this->equation_system['P'][$pivot_column] >= 0) {
            return 'done';
        } else {
            return $pivot_column;
        }
    }

    private function getPivotRow($pivot_column)
    {
        $equation_system = $this->equation_system;
        unset($equation_system['P']);
        foreach ($this->equation_system as $equation_index => $equation) {
            if ($equation['rhs'] >= 0 and $equation[$pivot_column] > 0) {
                $helper_array[$equation_index] = $equation['rhs'] / $equation[$pivot_column];
            }
        }
        asort($helper_array);
        return array_key_first($helper_array);
    }

    private function clearPivotColumn($pivot_column, $pivot_row)
    {
        $equation_system = $this->equation_system;
        $pivot_coefficient = $equation_system[$pivot_row][$pivot_column];
        foreach ($equation_system as $equation_index => $equation) {
            if ($equation[$pivot_column] !== 0) {
                $factor = $equation[$pivot_column] / $pivot_coefficient;
                if ($equation_index == $pivot_row) {
                    foreach ($equation as $variable => $coefficient) {
                        $equation_system[$equation_index][$variable] = $coefficient / $pivot_coefficient;
                    }
                    $pivot_coefficient = 1;
                } else {
                    foreach ($equation as $variable => $coefficient) {
                        $coefficient_new = $coefficient - ($factor * $equation_system[$pivot_row][$variable]);
                        if (abs($coefficient_new) < 10E-14) {
                            $equation_system[$equation_index][$variable] = 0;
                        } else {
                            $equation_system[$equation_index][$variable] = $coefficient_new;
                        }
                    }
                }
            }
        }
        $this->equation_system = $equation_system;
    }

    private function getHwList() {
        foreach($this->equation_system['P'] as $variable => $coefficient) {
            $coefficients = array_column($this->equation_system, $variable);
            $coefficients_zero = array_keys($coefficients, 0);
            $coefficients_one = array_keys($coefficients, 1);
            if (
                count($coefficients) == count($coefficients_zero) + 1
                and count($coefficients_one) == 1
                and $variable != 'P'
            ) {
                $equation_index = array_search(1, $coefficients);
                $hw_list[$variable] = $this->equation_system['s' . ($equation_index - 1)]['rhs'];
                $hw_list[$variable] = floor($hw_list[$variable]);
            }
        }
        return $hw_list;
    }

    private function extendHwArray($hw_list, $hardware, $van)
    {
        $van[6] = 0;
        for($van_index = 0; $van_index < count($van[1]); $van_index++) {
            $van[2][$van_index] = 0;
            $van[3][$van_index] = 0;
            for(
                $hardware_index = 0;
                $hardware_index < count($hardware);
                $hardware_index++
            ) {
                $result_index = 'hw' . $hardware_index . '_van' . $van_index;
                if (isset($hw_list[$result_index]) == false) {
                    $hw_list[$result_index] = 0;
                }
                //Units on van
                $hardware[$hardware_index][4 + $van_index] = $hw_list[$result_index];
                //Use value on van
                $use_value = $hw_list[$result_index] * $hardware[$hardware_index][3];
                $hardware[$hardware_index][$van_index + count($van[1]) + 4] = $use_value;
                $van[2][$van_index] += $use_value;
                if (isset($hardware[$hardware_index][3 * count($van[1]) + 4])) {
                    $hardware[$hardware_index][3 * count($van[1]) + 4] += $hw_list[$result_index];
                } else {
                    $hardware[$hardware_index][3 * count($van[1]) + 4] = $hw_list[$result_index];
                }
                //Weight on van
                $weight = $hw_list[$result_index] * $hardware[$hardware_index][2];
                $hardware[$hardware_index][$van_index + 2 * count($van[1]) + 4] = $weight;
                $van[3][$van_index] += $weight;
            }
                $van[4][$van_index] = $van[0] - $van[1][$van_index];
                $van[5][$van_index] = 100 * ($van[3][$van_index] / $van[4][$van_index]);
                $van[6] += $van[2][$van_index];
        }
        return array('hardware' => $hardware, 'van' => $van);
    }

    private function assembleEquationSystem($hardware, $van)
    {
        //assemble the objective function
        $number_slack_variables = count($van[1]) + count($hardware);
        for ($van_index = 0; $van_index < count($van[1]); $van_index++) {
            for ($hw_index = 0; $hw_index < count($hardware); $hw_index++) {
                $coefficient = 'hw' . $hw_index . '_van' . $van_index;
                $equation_system['P'][$coefficient] = -$hardware[$hw_index][3];
            }
        }

        for (
            $slack_index = 0;
            $slack_index < $number_slack_variables;
            $slack_index++
        ) {
            $equation_system['P']['s' . $slack_index] = 0;
        }
            $equation_system['P']['P'] = 1;
            $equation_system['P']['rhs'] = 0;


        //constraints
        for ($van_index = 0; $van_index < count($van[1]); $van_index++) {
            for ($hw_index = 0; $hw_index < count($hardware);$hw_index++) {
                $coefficient = 'hw' . $hw_index . '_van' . $van_index;
                for (
                    $van_index_inner = 0;
                    $van_index_inner < count($van[1]);
                    $van_index_inner++
                ) {
                    if ($van_index == $van_index_inner) {
                        $equation_system['s' . $van_index_inner][$coefficient] = $hardware[$hw_index][2];
                    } else {
                        $equation_system['s' . $van_index_inner][$coefficient] = 0;
                    }
                }
            for (
                $slack_index = 0;
                $slack_index < $number_slack_variables;
                $slack_index++
            ) {
                if ($van_index == $slack_index) {
                    $equation_system['s' . $van_index]['s' . $slack_index] = 1;
                } else {
                    $equation_system['s' . $van_index]['s' . $slack_index] = 0;
                }
            }

            }
            $max_payload = $van[0] - $van[1][$van_index];
            $equation_system['s' . $van_index]['P'] = 0;
            $equation_system['s' . $van_index]['rhs'] = $max_payload;


        }

        for ($hw_index = 0; $hw_index < count($hardware); $hw_index++) {
            $equation_index = 's' . ($hw_index + count($van[1]));
            for ($van_index = 0; $van_index < count($van[1]); $van_index++) {
                for (
                    $hw_index_inner = 0;
                    $hw_index_inner < count($hardware);
                    $hw_index_inner++
                ) {
                    $coefficient = 'hw' . $hw_index_inner . '_van' . $van_index;
                    if ($hw_index === $hw_index_inner) {
                        $equation_system[$equation_index][$coefficient] = 1;
                    } else {
                        $equation_system[$equation_index][$coefficient] = 0;
                    }
                }

                for (
                    $slack_index = 0;
                    $slack_index < $number_slack_variables;
                    $slack_index++
                ) {
                    if ($hw_index + 2 == $slack_index) {
                        $equation_system[$equation_index]['s' . $slack_index] = 1;
                    } else {
                        $equation_system[$equation_index]['s' . $slack_index] = 0;
                    }
                }
            }
            $equation_system[$equation_index]['P'] = 0;
            $equation_system[$equation_index]['rhs'] = $hardware[$hw_index][1];
        }
        $this->equation_system = $equation_system;
    }

    private function default()
    {
        $this->View->setTemplate('default');
    }

    private function checkData()
    {
        $files_in_place = $this->Model->checkFiles();
        if ($files_in_place === true) {
            $this->View->assign('hardware', $this->Model->getHardware());
            $this->View->assign('van', $this->Model->getVan());
        }
        $this->View->assign('files_in_place', $files_in_place);
        $this->View->setTemplate('check_data');

    }

    public function getOutput()
    {
        return $this->View->getOutput();
    }

}
