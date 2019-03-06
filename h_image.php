<?php

/**
 * Class h_image = Class that manipulates an image
 * @author Hakan Saplakoglu
 */
class h_image {

    private $image;

    /**
     * h_image constructor.
     * @param string $imgPath Path of image to be loaded into h_image, supported formats are JPG, PNG, BMP, GIF
     */
    public function __construct($imgPath) {
        switch(pathinfo($imgPath)['extension']) {
            case "jpeg":
            case "jpg":
                $this->image = imagecreatefromjpeg($imgPath);  // CREATE IMAGE FROM JPEG
                break;
            case "png":
                $this->image = imagecreatefrompng($imgPath);  // CREATE IMAGE FROM PNG
                break;
            case "bmp":
                $this->image = imagecreatefrombmp($imgPath);  // CREATE IMAGE FROM BMP
                break;
            case "gif":
                $this->image = imagecreatefromgif($imgPath);  // CREATE IMAGE FROM GIF
                break;
            default:
                echo "Unsupported image file type, use only jpg, png, bmp, or gif";  // UNSUPPORTED TYPE
        }
    }

    /**
     * @return int Width of current image
     */
    public function getWidth() {
        return imagesx($this->image);
    }

    /**
     * @return int Height of current image
     */
    public function getHeight() {
        return imagesy($this->image);
    }

    /**
     * @param int $width Width to resize
     * @param int $height Height to resize
     * @param bool $keepAspect If true, will not stretch, if false, may stretch
     * @param int $offset_x X Offset starting from top-right of image, to resize from
     * @param int $offset_y Y Offset starting from top-right of image, to resize from
     * @throws Exception Unable to resize image
     * @throws Exception Both width and height cannot be null
     */
    public function resizeImage($width = null, $height = null, $keepAspect = true, $offset_x = 0, $offset_y = 0) {
        $origWidth = $this->getWidth();  // GET ORIGINAL HEIGHT OF THE IMAGE
        $origHeight = $this->getHeight();  // GET ORIGINAL WIDTH OF THE IMAGE

        $origAspect = $origWidth / $origHeight;  // CALCULATE ORIGINTAL ASPECT RATIO

        if($width === null && $height === null) {
            throw new Exception("Both width and height cannot be null.");
        } elseif($width === null) {
            $width = $height * $origAspect;  // CALCULATE VALUE OF WIDTH BASED ON ASPECT RATIO
        } elseif($height === null) {
            $height = $width * (1 / $origAspect);  // CALCULATE VALUE OF HEIGHT BASED ON ASPECT RATIO
        }

        $newAspect = $width / $height;  // CALCULATE NEW ASPECT RATIO

        $sampleWidth = $origWidth;
        $sampleHeight = $origHeight;

        if($keepAspect) {
            if($newAspect >= $origAspect) {
                $sampleHeight = $origHeight * ($origAspect / $newAspect);  // PRESERVE ENTIRE WIDTH, HEIGHT CHANGES
            } else {
                $sampleWidth = $origWidth * ($newAspect / $origAspect);  // PRESERVE ENTIRE HEIGHT, WIDTH CHANGES
            }
        }

        $this->resize_image($width, $height, $sampleWidth, $sampleHeight, $offset_x, $offset_y);
    }

    /**
     * @param string $imgPath Destination image path
     * @param int $quality Quality of image (for JPEG images only), from -1 to 100
     */
    public function saveImage($imgPath, $quality = -1) {
        switch(pathinfo($imgPath)['extension']) {
            case "jpeg":
            case "jpg":
                $this->image = imagejpeg($this->image, $imgPath, $quality);  // SAVE JPEG IMAGE
                break;
            case "png":
                $this->image = imagepng($this->image, $imgPath);  // SAVE PNG IMAGE
                break;
            case "bmp":
                $this->image = imagebmp($this->image, $imgPath);  // SAVE BMP IMAGE
                break;
            case "gif":
                $this->image = imagegif($this->image, $imgPath);  // SAVE GIF IMAGE
                break;
            default:
                echo "Unsupported save image file type, use only jpg, png, bmp, or gif";  // UNSUPPORTED TYPE
        }
    }

    /**
     * @param int $width Width of new image
     * @param int $height Height of new image
     * @param int $sWidth Sampling width from original image
     * @param int $sHeight Sampling height from original image
     * @param int $offset_x X-Offset of source image
     * @param int $offset_y Y-Offset of source image
     * @throws Exception Unable to resize image
     */
    protected function resize_image($width, $height, $sWidth, $sHeight, $offset_x, $offset_y) {
        $newImage = imagecreatetruecolor($width, $height);
        if(imagecopyresampled($newImage, $this->image,
            0, 0, $offset_x, $offset_y,
            $width, $height, $sWidth - $offset_x, $sHeight - $offset_y)) {
            $this->image = $newImage;
        } else {
            throw new Exception("Unable to resize image");
        }
    }

}