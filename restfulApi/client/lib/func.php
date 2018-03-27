<?php

function showtable($array) {
    echo '<div>';
    echo '<table border="1px" cellspacing="0">';
    echo '<head>';
    echo '<td>*';
    echo '</td>';
    foreach ((array) $array as $key => $value) {
        foreach ((array) $value as $key1 => $value1) {
            echo '<th>';
            echo '<span>' . $key1 . '</span>';
            echo '</th>';
        }
        break;
    }
    echo '</head>';
    foreach ((array) $array as $key => $value) {
        echo '<tr>';
        echo '<td>';
        echo '<span>' . $key . '</span>';
        echo '</td>';
        if (is_array($value)) {
            foreach ((array) $value as $key1 => $value1) {
                echo '<td>';
                if (is_array($value1)) {
                    showtable($value1);
                } else {
                    echo '<span>' . $value1 . '</span>';
                    if (!empty($key1) && in_array($key1, ['pub_date', 'updated_at']) && !empty($value1)) {
                        echo '<small>(' . Times::local('Y-m-d', $value1) . ')</small>';
                    }
                }
                echo '</td>';
            }
        } else {
            echo '<td>';
            echo '<span>' . $value . '</span>';
            echo '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
}
