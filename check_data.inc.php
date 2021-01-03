<h2>Provided data</h2>

<?php

if ($this->_['files_in_place'] === true):

?>

<table>
    <tr>
        <th>Hardware</th>
        <th>Needed units</th>
        <th>Weight per unit [kg]</th>
        <th>Use value</th>
    </tr>
<?php

    foreach($this->_['hardware'] as $unit) {
        printf(
            '<tr>
                <td class="text">%1$s</td>
                <td>%2$u</td>
                <td>%3$1.3f</td>
                <td>%4$u</td>
            </tr>',
            $unit[0],
            $unit[1],
            $unit[2],
            $unit[3]
        );
    }

    echo '</table>
        <div>';

    $number_vans = count($this->_['van'][1]);
    echo '<ul><li>Number of vans: ' . $number_vans . '</li>';
    echo '<li>Max capacity per van: ' . $this->_['van'][0] . " kg</li>";
    echo '<li>Weight of the drivers:
        <ul>';
    foreach ($this->_['van'][1] as $weight_driver) {
        printf('<li>%3.1f kg</li>', $weight_driver);
    }
    echo '</ul>
        </ul>
        </div>';

    echo '<button onclick="display(\'output_result\')">Process data</button>';
else:
    echo '<div class="error">Data is not complete.</div>';
endif;

?>

<button onclick="display('check_data')">Reload data</button>
