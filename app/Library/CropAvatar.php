<?php


namespace App\Library;

class CropAvatar {
    private $src;
    private $data;
    private $file;
    private $dst;
    private $filedst;
    private $type;
    private $extension;
    private $msg=null;

    function __construct($src, $data, $file, $filedst, $dstwidth, $dstheight) {
        $this -> filedst=$filedst;
        $this -> setSrc($src);
        $this -> setData($data);
        $this -> setFile($file);
        $this -> crop($this -> src, $this -> dst, $this -> data, $dstwidth, $dstheight);
    }

    private function setSrc($src) {
        if (!empty($src)) {
            $type = exif_imagetype($src);
            if ($type) {
                $this -> src = $src;
                $this -> type = $type;
                $this -> extension = image_type_to_extension($type);
                $this -> setDst();
            }
        }
    }

    private function setData($data) {
        if (!empty($data)) {
            $this -> data = json_decode(stripslashes($data));
        }
    }

    private function setFile($file) {
        $errorCode = $file['error'];

        if ($errorCode === UPLOAD_ERR_OK) {
            $type = exif_imagetype($file['tmp_name']);

            if ($type) {
                $extension = image_type_to_extension($type);
                //$src = 'img/' . date('YmdHis') . '.original' . $extension;
                $src = public_path($this->filedst . '.original' . $extension);

                if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG ||$type==IMAGETYPE_JPEG2000) {

                    if (file_exists($src)) {
                        unlink($src);
                    }

                    $result = move_uploaded_file($file['tmp_name'], $src);

                    if ($result) {
                        $this -> src = $src;
                        $this -> type = $type;
                        $this -> extension = $extension;
                        $this -> setDst();
                    } else {
                        //$this -> msg = 'Failed to save file';
                        $this -> msg = '上传原始文件失败';
                    }
                } else {
                    //$this -> msg = 'Please upload image with the following types: JPG, PNG, GIF';
                    $this -> msg = '请上传以下格式的图片文件：JPG、PNG、GIF';
                }
            } else {
                //$this -> msg = 'Please upload image file';
                $this -> msg = '请上传图片文件';
            }
        } else {
            $this -> msg = $this -> codeToMessage($errorCode);
        }
    }

    private function setDst() {
        //$this -> dst = 'img/' . date('YmdHis') . '.png';
        $this -> dst =  $this->filedst . '.png';
    }

    private function crop($src, $dst, $data, $dstwidth ,$dstheight) {
        if (!empty($src) && !empty($dst) && !empty($data)) {
            switch ($this -> type) {
                case IMAGETYPE_GIF:
                    $src_img = imagecreatefromgif($src);
                    break;

                case IMAGETYPE_JPEG:
                    $src_img = imagecreatefromjpeg($src);
                    break;

                case IMAGETYPE_PNG:
                    $src_img = imagecreatefrompng($src);
                    break;
            }

            if (!isset($src_img)||!$src_img) {
                //$this -> msg = "Failed to read the image file";
                $this -> msg = "读取上传文件失败";
                return;
            }

            $size = getimagesize($src);
            $size_w = $size[0]; // natural width
            $size_h = $size[1]; // natural height

            $src_img_w = $size_w;
            $src_img_h = $size_h;

            $degrees = $data -> rotate;

            // Rotate the source image
            if (is_numeric($degrees) && $degrees != 0) {
                // PHP's degrees is opposite to CSS's degrees
                $new_img = imagerotate( $src_img, -$degrees, imagecolorallocatealpha($src_img, 0, 0, 0, 127) );

                imagedestroy($src_img);
                $src_img = $new_img;

                $deg = abs($degrees) % 180;
                $arc = ($deg > 90 ? (180 - $deg) : $deg) * M_PI / 180;

                $src_img_w = $size_w * cos($arc) + $size_h * sin($arc);
                $src_img_h = $size_w * sin($arc) + $size_h * cos($arc);

                // Fix rotated image miss 1px issue when degrees < 0
                $src_img_w -= 1;
                $src_img_h -= 1;
            }

            $tmp_img_w = $data -> width;
            $tmp_img_h = $data -> height;
            if($tmp_img_h<$dstheight)
                $dst_img_h=$tmp_img_h;
            else
                $dst_img_h = $dstheight;
            if($tmp_img_w<$dstwidth)
                $dst_img_w=$tmp_img_w;
            else
                $dst_img_w = $dstwidth;


            $src_x = $data -> x;
            $src_y = $data -> y;

            if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
                $src_x = $src_w = $dst_x = $dst_w = 0;
            } elseif ($src_x <= 0) {
                $dst_x = -$src_x;
                $src_x = 0;
                $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
            } else{//if ($src_x <= $src_img_w)
                $dst_x = 0;
                $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
            }

            if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
                $src_y = $src_h = $dst_y = $dst_h = 0;
            } elseif ($src_y <= 0) {
                $dst_y = -$src_y;
                $src_y = 0;
                $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
            } else{//if ($src_y <= $src_img_h)
                $dst_y = 0;
                $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
            }

            // Scale to destination position and size
            $ratio = $tmp_img_w / $dst_img_w;
            $dst_x /= $ratio;
            $dst_y /= $ratio;
            $dst_w /= $ratio;
            $dst_h /= $ratio;

            $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

            // Add transparent background to destination image
            imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
            imagesavealpha($dst_img, true);

            $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

            if ($result) {
                if (!imagepng($dst_img, public_path($dst))) {
                    //$this -> msg = "Failed to save the cropped image file";
                    $this -> msg = "上传剪裁图片失败";
                }
            } else {
                //$this -> msg = "Failed to crop the image file";
                $this -> msg = "剪裁图片失败";
            }

            imagedestroy($src_img);
            imagedestroy($dst_img);
        }
    }

    private function codeToMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                //$message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                $message = '上传上午图片超出配置最大文件大小';
                break;

            case UPLOAD_ERR_FORM_SIZE:
                //$message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                $message = '上传的图片超出HTML支持最大文件大小';
                break;

            case UPLOAD_ERR_PARTIAL:
                //$message = 'The uploaded file was only partially uploaded';
                $message = '只有部分图片文件被上传';
                break;

            case UPLOAD_ERR_NO_FILE:
                //$message = 'No file was uploaded';
                $message = '没有图片文件被上传';
                break;

            case UPLOAD_ERR_NO_TMP_DIR:
                //$message = 'Missing a temporary folder';
                $message = '缺少上传临时路径';
                break;

            case UPLOAD_ERR_CANT_WRITE:
                //$message = 'Failed to write file to disk';
                $message = '图片文件上传服务器失败';
                break;

            case UPLOAD_ERR_EXTENSION:
                //$message = 'File upload stopped by extension';
                $message = '文件上传已被扩展停止';
                break;

            default:
                //$message = 'Unknown upload error';
                $message = '未知错误';
        }

        return $message;
    }

    public function getResult() {
        return !empty($this -> data) ? $this -> dst : $this -> src;
    }

    public function getMsg() {
        return $this -> msg;
    }
}
