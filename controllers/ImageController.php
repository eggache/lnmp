<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class ImageController extends Controller
{
    public $pictureExtToFormat = [
        'jpg' => 'JPEG',
        'jpeg'=> 'JPEG',
        'png' => 'PNG',
        'gif' => 'GIF',
        'webp' => 'WEBP',
        'bmp' => 'BMP',
    ];

    private $pictureFormatToTypeid = [
        'JPEG' => IMAGETYPE_JPEG,
        'GIF'  => IMAGETYPE_GIF,
        'PNG'  => IMAGETYPE_PNG,
        'BMP' => IMAGETYPE_BMP,
    ];

    public function __construct()
    {
        
    }

    public function getImageFormat($extension)
    {
        $extension = strtolower($extension);
        return isset($this->pictureExtToFormat[$extension]) ? $this->pictureExtToFormat[$extension] : NULL;
    }

    public function resizeImage($tempName, $conf)
    {
        if ($tempName == '' || !file_exists($tempName)) {
            return 'PHOTO_RESIZE_FILE_NOT_FOUND';
            // 目的文件检查
        }

        if (empty($conf['format'])) {
            $extension = strtolower(pathinfo($desFile, PATHINFO_EXTENSION));
            $conf['format'] = $this->getImageFormat($extension);
            if ($conf['format'] === NULL) {
                return 'PHOTO_FORMAT_NOT_SUPPORT';
            }
        }

        if (!empty($conf['originalFormat']) && $this->isInvalidImage($tempName, $conf['originalFormat'])) {
            return 'PHOTO_CONTENT_INVAILD';
        }

        $sourceImgBlob = file_get_contents($tempName);
        $err = $this->resizeImageBlob($sourceImgBlob, $outputImgBlob, $conf);
        if ($err !== false) {
            return $err;
        }
        $hash = md5($outputImgBlob);
        $desFile = "../image/" . $hash . "." . $conf['originalFormat'];

        //成功, 写入文件
        !file_exists($dir = dirname($desFile)) && mkdir($dir, 0775, true);
        $wroteBytes = file_put_contents($desFile, $outputImgBlob);
        // 检查文件是否写入成功
        if ($wroteBytes === false) {
            return 'PHOTO_RESIZE_FILE_WRITE_FAILED';
        }
        return false;
    }

    /**
     * 检查图片是否是非法的(实际内容不是图片or不是声明的格式)
     * @param $imgFile   图片文件path
     * @param $declaredFormat  声明的格式
     *
     * @return  true:非法  false:正常
     */
    public function isInvalidImage($imgFile, $declaredFormat)
    {
        //图片太小肯定也不合法, 同时会造成exif_imagetype报错
        if (filesize($imgFile) < 12) {
            return true;
        }
        foreach ($this->pictureFormatToTypeid as $format => $typeid) {
            if ($format === $declaredFormat) {
                return exif_imagetype($imgFile) !== $typeid;
            }
        }
        //如果$declaredFormat不在可检查的范围内(比如webp), 则不进行检查,并认为合法
        return false;
    }

    public function resizeImageBlob($input, &$output, $conf)
    {
        // 解析配置
        $width   = isset($conf['width'])   ? intval($conf['width'])   : 0;
        $height  = isset($conf['height'])  ? intval($conf['height'])  : 0;
        $quality = isset($conf['quality']) ? intval($conf['quality']) : 100;
        $format  = isset($conf['format'])  ? $conf['format'] : 'JPEG';
        $removeProfile = isset($conf['removeProfile']) ? $conf['removeProfile'] : true;
        $keepProportion = empty($conf['keepProportion']) ? false : true;

        // 初始化对象
        $wand = NewMagickWand();
        if(!MagickReadImageBlob($wand , $input)) {
            return 'PHOTO_NEW_MAGICKREAD_ERROR';
        }
        // 文件mimetype检查
        if (stripos(MagickGetImageMimeType($wand), 'image') !== 0) {
            return 'PHOTO_RESIZE_FILE_TYPE_ERROR';
        }
        // 移除profile
        if ($removeProfile) {
            if (!MagickRemoveImageProfiles($wand)) {
                return 'IMG_RM_PROFILE_ERROR';
            }
        }
        // 取高宽
        $w1 = $w = MagickGetImageWidth($wand);
        $h1 = $h = MagickGetImageHeight($wand);
        // 高宽均约束
        if ($width > 0 && $height > 0 && ($width < $w || $height < $h)) {
            if ($keepProportion) {
                $oriPortion = $w / $h;
                $newPortion = $width / $height;
                if ($oriPortion > $newPortion) { //原图长宽比例大于目标图(比目标图长),则宽度设为目标值,保持比例后高度必然小于目标值
                    $h *= $width / $w;
                    $w = $width;
                } else {
                    $w *= $height / $h;
                    $h = $height;
                }
            } else {
                $w = $width;
                $h = $height;
            }
        // 仅约束宽度
        } elseif ($width > 0 && $height == 0 && $width <  $w) {
            $h *= $width / $w;
            $w = $width;
        // 仅约束高度
        } elseif ($width == 0 && $height > 0 && $height < $h) {
            $w *= $height / $h;
            $h = $height;
        }
        // 重置尺寸
        if ($w1 != $w || $h1 != $h) {
            if (!MagickResizeImage($wand, $w, $h, MW_LanczosFilter, 1)) {
                return 'IMG_RESIZE_ERROR';
            }
        }
        // 设置格式
        if (!MagickSetFormat($wand, $format)) {
            return 'IMG_SET_FORMAT_ERR';
        }
        // JPEG设置processive encoding
        if ($format === 'JPEG') {
            MagickSetImageInterlaceScheme($wand, MW_LineInterlace);
        }
        // 设置压缩率
        if (MagickGetImageCompressionQuality($wand) > $quality) {
            if (!MagickSetImageCompressionQuality($wand, $quality)) {
                return 'IMG_COMPRESS_ERROR';
            }
        }
        if (isset($conf['watermark'])) {
            $wand = self::addWatermark($wand, $conf['watermark']);
        }
        $output = MagickGetImagesBlob($wand);
        return false;
    }

    public static function addWatermark($sourceWand, $watermarkimage, $padding_right = 15, $padding_bottom = 15)
    {
        if (!file_exists($watermarkimage)) {
            return $sourceWand;
        }

        $compositeWand = NewMagickWand();
        $ret = file_exists($watermarkimage);
        if (!MagickReadImage($compositeWand, $watermarkimage)) {
            MagickDeconstructImages($compositeWand);
            return $sourceWand;
        }
        $width = MagickGetImageWidth($sourceWand);    
        $height = MagickGetImageHeight($sourceWand);
        $waterwidth =  MagickGetImageWidth($compositeWand);
        $waterheight =  MagickGetImageHeight($compositeWand);

        $xoffset = $width - $waterwidth - $padding_right;
        $yoffset = $height - $waterheight - $padding_bottom;
        $xoffset = $xoffset > 0 ? $xoffset : 0;
        $yoffset = $yoffset > 0 ? $yoffset : 0;

        //combining the images
        MagickCompositeImage($sourceWand, $compositeWand, MW_ScreenCompositeOp, $xoffset, $yoffset);
        MagickDeconstructImages($compositeWand);
        return $sourceWand;
    }
}
