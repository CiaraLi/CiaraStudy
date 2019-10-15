<?php
/**
 * Notes:图片缩放
 * User: Ciara
 * Date: 2019/10/15
 * Time: 14:00
 */

namespace common\libraries;


class PicZoom
{
    private $back;
    private $front;
    private $final;
    private $zoom;

    /**
     * PicZoom constructor.
     * @param $file 处理图片
     * @param string $new 结果图片
     * @param string $bk 背景图片
     */
    function __construct($file,$new=null )
    {
        $this->front = UrlBuilder::init($file??"/img/hd.png");
        $this->back = UrlBuilder::init( "/img/bk.png");
        $this->final = UrlBuilder::init($new??"/img/new.png");
        echo " <img src='{$this->front->url()}'> ";

    }

    /**
     * Notes: 图片缩放
     * User: Ciara
     * Date: 2019/10/15
     * Time: 14:17
     */
    public function zoom($zoom)
    {
        $this->zoom = $zoom;
        list($width, $height) = getimagesize($this->front->fullPath());
        $this->addBackgrund($width, $height);
        $this->cutSize($width, $height);
        return $this;
    }

    /*
    * 创建图像对象
    * @param $imgFile 图片路径
    * @param $imgExt  图片扩展名
    * @return $im 图像对象
    **/
    function picCreate($width, $height)
    {
        return imagecreatetruecolor($width, $height);
    }

    /*
    * 创建图像对象
    * @param $imgFile 图片路径
    * @param $imgExt  图片扩展名
    * @return $im 图像对象
    **/
    function picOpen($imgFile, $imgExt=0)
    {
        $im = null;
        switch ($imgExt) {
            case 1:
                $im = imagecreatefromgif($imgFile);
                break;
            case 2:
                $im = imagecreatefromjpeg($imgFile);
                break;
            case 3:
            default:
                $im = imagecreatefrompng($imgFile);
                break;
        }
        return $im;
    }

    /*
    * 保存图像对象
    * @param $imgFile 图片路径
    * @param $savePath 图片路径
    * @param $imgExt  图片扩展名
    * @return $im 图像对象
    **/
    function picSave($imgFile, $savePath, $imgExt =0)
    {
        $im = null;

        switch ($imgExt) {
            case 1:
                $im = imagegif($imgFile, $savePath);
                break;
            case 2:
                $im = imagejpeg($imgFile, $savePath);
                break;
            case 3:
            default:
                $im = imagepng($imgFile, $savePath);
                break;
        }
        return $im;
    }

    function addBackgrund($width, $height)
    {
        $bkImgObj = $this->picOpen($this->back->fullPath());

        $waterImgObj = $this->picOpen($this->front->fullPath() );

        $new_height = $height * $this->zoom / 100;
        $new_width = $width * $this->zoom / 100;

        //添加水印图片
        $x = ($height - $new_height) / 2;
        $y = ($width - $new_width) / 2;

        imagealphablending($bkImgObj, false);//这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;
        imagesavealpha($bkImgObj, true);//这里很重要,意思是不要丢了$thumb图像的透明色;

        imagecopyresampled($bkImgObj, $waterImgObj, $x, $y, 0, 0, $new_width, $new_height, $width, $height);

        //输出图片
        imagepng($bkImgObj, $this->final->fullPath());

        //销毁图像资源
        imagedestroy($bkImgObj);
        imagedestroy($waterImgObj);
//        echo " <img src='{$this->final->url()}'> ";
    }

    /**
     * Notes:剪裁为目标大小
     * User: Ciara
     * Date: 2019/10/15
     * Time: 14:49
     * @param $width
     * @param $height
     */
    function cutSize($width, $height)
    {

        $finalimage = $this->picCreate($width, $height);
        $cutImg = $this->picOpen($this->final->fullPath());

        imagealphablending($finalimage, false);//这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;
        imagesavealpha($finalimage, true);//这里很重要,意思是不要丢了$thumb图像的透明色;

        imagecopyresampled($finalimage, $cutImg, 0, 0, 0, 0, $width, $height, $width, $height);


        $final = UrlBuilder::init( "/img/final/".rand().".png");
        $final->check();
        //输出图片
        $this->picSave($finalimage, $final->fullPath());

        //销毁图像资源
        imagedestroy($finalimage);
        imagedestroy($cutImg);
        echo " <img src='{$final->url()}'> ";
    }
}