<?php

use Illuminate\Support\Str;
/**
 * String format
 *
 * @param mixed $msg     msg
 * @param mixed ...$vars vars
 *
 * @return string
 */
function str_format($msg, ...$vars)
{
    $msg = preg_replace_callback('#\{\}#', function ($r) {
        static $i = 0;
        return '{' . ($i++) . '}';
    }, $msg);
    return str_replace(
        array_map(function ($k) {
            return '{' . $k . '}';
        }, array_keys($vars)),
        array_values($vars),
        $msg
    );
}

function convertSlugToId($slug)
{
    $i = 0;
    for($i = strlen($slug) - 1; $i >= 0; $i--){
        if($slug[$i] == '-'){
            break;
        }
    }
    $id = Str::substr($slug, $i + 1, strlen($slug) - $i);

    return $id;
}

function convertSlugToSlug($slug)
{
    $i = 0;
    for($i = strlen($slug) - 1; $i >= 0; $i--){
        if($slug[$i] == '-'){
            break;
        }
    }
    $fullname = Str::substr($slug, 1, $i);

    return $fullname;
}

/**
 * Generate uuid
 *
 * @return string
 */
function generateUuid()
{
    return Illuminate\Support\Str::uuid()->toString();
}


/**
 * Get user
 *
 * @return User
 */
function user()
{
    return auth('user')->user();
}

function removePrefix($url)
{
    $parse = parse_url($url);
    if($parse['scheme'] == 'http' || $parse['scheme'] == 'https') {
        return $parse['path'];
    }
    return $url;
}

function numInWords($num)
    {
        $nwords = array(
            0                   => 'không',
            1                   => 'một',
            2                   => 'hai',
            3                   => 'ba',
            4                   => 'bốn',
            5                   => 'năm',
            6                   => 'sáu',
            7                   => 'bảy',
            8                   => 'tám',
            9                   => 'chín',
            10                  => 'mười',
            11                  => 'mười một',
            12                  => 'mười hai',
            13                  => 'mười ba',
            14                  => 'mười bốn',
            15                  => 'mười lăm',
            16                  => 'mười sáu',
            17                  => 'mười bảy',
            18                  => 'mười tám',
            19                  => 'mười chín',
            20                  => 'hai mươi',
            30                  => 'ba mươi',
            40                  => 'bốn mươi',
            50                  => 'năm mươi',
            60                  => 'sáu mươi',
            70                  => 'bảy mươi',
            80                  => 'tám mươi',
            90                  => 'chín mươi',
            100                 => 'trăm',
            1000                => 'nghìn',
            1000000             => 'triệu',
            1000000000          => 'tỷ',
            1000000000000       => 'nghìn tỷ',
            1000000000000000    => 'ngàn triệu triệu',
            1000000000000000000 => 'tỷ tỷ',
        );
        $separate = ' ';
        $negative = ' âm ';
        $rltTen   = ' linh ';
        $decimal  = ' phẩy ';
        if (!is_numeric($num)) {
            $w = '#';
        } else if ($num < 0) {
            $w = $negative . $this->numInWords(abs($num));
        } else {
            if (fmod($num, 1) != 0) {
                $numInstr    = strval($num);
                $numInstrArr = explode(".", $numInstr);
                $w           = $this->numInWords(intval($numInstrArr[0])) . $decimal . $this->numInWords(intval($numInstrArr[1]));
            } else {
                $w = '';
                if ($num < 21) // 0 to 20
                {
                    $w .= $nwords[$num];
                } else if ($num < 100) {
                    // 21 to 99
                    $w .= $nwords[10 * floor($num / 10)];
                    $r = fmod($num, 10);
                    if ($r > 0) {
                        $w .= $separate . $nwords[$r];
                    }

                } else if ($num < 1000) {
                    // 100 to 999
                    $w .= $nwords[floor($num / 100)] . $separate . $nwords[100];
                    $r = fmod($num, 100);
                    if ($r > 0) {
                        if ($r < 10) {
                            $w .= $rltTen . $separate . $this->numInWords($r);
                        } else {
                            $w .= $separate . $this->numInWords($r);
                        }
                    }
                } else {
                    $baseUnit     = pow(1000, floor(log($num, 1000)));
                    $numBaseUnits = (int) ($num / $baseUnit);
                    $r            = fmod($num, $baseUnit);
                    if ($r == 0) {
                        $w = $this->numInWords($numBaseUnits) . $separate . $nwords[$baseUnit];
                    } else {
                        if ($r < 100) {
                            if ($r >= 10) {
                                $w = $this->numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm ' . $this->numInWords($r);
                            }
                            else{
                                $w = $this->numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm linh ' . $this->numInWords($r);
                            }
                        } else {
                            $baseUnitInstr      = strval($baseUnit);
                            $rInstr             = strval($r);
                            $lenOfBaseUnitInstr = strlen($baseUnitInstr);
                            $lenOfRInstr        = strlen($rInstr);
                            if (($lenOfBaseUnitInstr - 1) != $lenOfRInstr) {
                                $numberOfZero = $lenOfBaseUnitInstr - $lenOfRInstr - 1;
                                if ($numberOfZero == 2) {
                                    $w = $this->numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm linh ' . $this->numInWords($r);
                                } else if ($numberOfZero == 1) {
                                    $w = $this->numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm ' . $this->numInWords($r);
                                } else {
                                    $w = $this->numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . $separate . $this->numInWords($r);
                                }
                            } else {
                                $w = $this->numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . $separate . $this->numInWords($r);
                            }
                        }
                    }
                }
            }
        }
        return $w;
    }
