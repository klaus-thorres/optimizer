<h1>Results</h1>

<?php
for ($van_index = 0; $van_index < count($this->_['van'][1]); $van_index++):
    printf('<h2>Van %1$u</h2>', $van_index + 1);

?>

<table>
    <tr>
        <th>Hardware</th>
        <th>Needed units</th>
        <th>Units on van</th>
        <th>Weight per unit [kg]</th>
        <th>Weight [kg]
        <th>Use value per unit</th>
        <th>Use value</th>
    </tr>

<?php

    foreach($this->_['hardware'] as $unit) {
        if ($unit[$van_index + 4] == 0) {
            continue;
        }
        printf(
            '<tr><td class="text">%1$s</td>
            <td>%2$u</td>
            <td>%3$.0f</td>
            <td>%4$.3f</td>
            <td>%5$.3f</td>
            <td>%6$u</td>
            <td>%7$s</td></tr>',
            $unit[0],
            $unit[1],
            $unit[$van_index + 4],
            $unit[2],
            $unit[$van_index + 2 * count($this->_['van'][1]) + 4],
            $unit[3],
            number_format(
                $unit[$van_index + count($this->_['van'][1]) + 4],
                0,
                '.',
                ','
            )
        );
    }
    echo '</table>';
    printf (
        '<ul>
        <li>Use value: %1$s</li>
        <li>Payload: %2$.1f kg of %3$.1f kg (%4$.2f &percnt;)</li>
        </ul>',
        number_format($this->_['van'][2][$van_index], 0, '.', ','),
        $this->_['van'][3][$van_index],
        $this->_['van'][4][$van_index],
        $this->_['van'][5][$van_index]
    );

endfor;
?>
<h2>Total</h2>

<table>
    <tr>
        <th>Hardware</th>
        <th>Needed units</th>
        <th>Units total</th>
        <th>Weight per unit [kg]</th>
        <th>Use value per unit</th>
    </tr>
<?php

foreach($this->_['hardware'] as $unit) {
    printf(
        '<tr><td class="text">%1$s</td>
        <td>%2$u</td>
        <td>%3$.0f</td>
        <td>%4$.3f</td>
        <td>%5$u</td></tr>',
        $unit[0],
        $unit[1],
        $unit[3 * $van_index + 4],
        $unit[2],
        $unit[3],
   );
}

?>
</table>

<ul>
    <li>Use value: <?= number_format($this->_['van'][6], 0, '.', ',') ?></li>
</ul>



