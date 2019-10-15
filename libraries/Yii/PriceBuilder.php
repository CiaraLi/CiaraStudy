<?php
/**
 * Notes:价格处理类
 * User: Ciara
 * Date: 2019/8/28
 * Time: 14:26
 */

namespace common\libraries;



class PriceBuilder
{
    /**
     * Notes:将元 转为分
     * User: Ciara
     * Date: 2019/8/28
     * Time: 14:24
     * @param float $price  十进制价格（元）
     * @return string  十进制价格（分）0位小数
     */
    static function priceToFen($price) {
        return  bcmul($price,100, 0);
    }

    /**
     * Notes:将分 转为元
     * User: Ciara
     * Date: 2019/8/28
     * Time: 14:24
     * @param float $price   十进制价格（分）
     * @return string  十进制价格（元）2位小数
     */
    static function priceFromFen($price ) {
        return bcdiv($price,100, 2);
    }


    /**
     * Notes: 价格加法计算
     * User: Ciara
     * Date: 2019/8/28
     * Time: 14:24
     * @param float $price1   十进制价格（分）
     * @param float $price2   十进制价格（分）
     * @return string  十进制价格（分）0位小数
     */
    static function priceAdd($price1 ,$price2) {
        return bcadd($price1, $price2, 0);
    }


    /**
     * Notes: 价格减法计算
     * User: Ciara
     * Date: 2019/8/28
     * Time: 14:24
     * @param float $price1   十进制价格（分）
     * @param float $price2   十进制价格（分）
     * @return string  十进制价格（分）0位小数
     */
    static function priceSub($price1 ,$price2) {
        return bcsub($price1,$price2, 0);
    }



    /**
     * Notes:
     * User: Ciara
     * Date: 2019/8/28
     * Time: 14:38
     * @param $price int  十进制价格（分）
     * @param $discount int   十进折扣（%）
     * @return string
     */
    static function priceDiscount($price,$discount ){
        return bcsub($price,bcdiv($discount,100), 0);
    }
}