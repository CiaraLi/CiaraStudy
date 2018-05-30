<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of strTest
 *
 * @author ciara
 */
class compare {

    function checkSub($strA, $strB, $i = 0, $finded = '', $main = true) {
        $lenA = strlen($strA);
        $lenB = strlen($strB);
        if ($i < $lenB) {
            $findChar = $strB[intval($i)];
            $posA = strpos($strA, $findChar);
            if ($posA !== false) {
                $finded .= $findChar;
                if ($lenB - 1 > $i) {
                    $finded = $this->check_Sub(substr($strA, $posA + 1), $strB, $i + 1, $finded, false);
                }
            }
        }

        if ($main) {
            if ($lenB > 0 && $finded == $strB) {
                return true;
            } else {
                return false;
            }
        } else {
            return $finded;
        }
    }

    function check_sub($strA, $strB, $i = 0, $finded = '', $main = true) {
        $lenA = strlen($strA);
        $lenB = strlen($strB);
        if ($i < $lenB) {
            $findChar = $strB[intval($i)];
            $posA = strpos(strtolower($strA), strtolower($findChar));
            if ($posA !== false) {
                $finded .= $findChar;
                if ($lenB - 1 > $i) {
                    $finded = $this->check_sub(substr($strA, $posA + 1), $strB, $i + 1, $finded, false);
                }
            }
        }

        if ($main) {
            if ($lenB > 0 && $finded == $strB) {
                return true;
            } else {
                return false;
            }
        } else {
            return $finded;
        }
    }

}

$unit = new compare();

var_dump($unit->checkSub('ABEDACDSEVB', 'BAaD'));
