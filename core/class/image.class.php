<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * image.class.php
 *
 * 图片上传类
 *
 * @package class
 * @author awfigq <awfigq@qq.com>
 */
class Image
{
	/**
	 * 文件信息
	 */
	var $file = array();
	/**
	 * 保存目录
	 */
	var $dir = 'public';
	/**
	 * 错误代码
	 */
	var $error_code = 0;
	/**
	 * 文件上传最大KB
	 */
	var $max_size = 2048;

	function Image()
	{
		
	}

	/**
	 * 处理上传文件
	 * @param array $file 上传的文件
	 * @param string $dir 保存的目录
	 * @return bool
	 */
	function init($file, $dir = 'temp')
	{
		if(!is_array($file) || empty($file) || !$this->isUploadFile($file['tmp_name']) || trim($file['name']) == '' || $file['size'] == 0)
		{
			$this->file = array();
			$this->error_code = -1;
			return false;
		}
		else
		{
			$file['size'] = intval($file['size']);
			$file['name'] =  trim($file['name']);
			$file['thumb'] = '';
			$file['ext'] = $this->fileExt($file['name']);
			$file['name'] =  htmlspecialchars($file['name'], ENT_QUOTES);
			$file['is_image'] = $this->isImageExt($file['ext']);
			$file['is_convert'] = false;

			$info = $this->getImageInfo($file['tmp_name']);
			$type_1=$this->get_image_info($file['tmp_name']);
			$type_1=$type_1['type'];
			/*
			 * 修复本地上传，GIF动态图片 会自动转化为JPEG的BUG
			 */
			/*
			if($info['type'] != 'jpg' && $info['type'] != 'jpeg')
			{
				$file['ext'] = $ext;
				$file['is_convert'] = true;
			}
			*/
			$file['file_dir'] = $this->getTargetDir($dir);
			$file['prefix'] = md5(microtime(true)).random('6');
			$file['target'] = $file['file_dir'].'/'.$file['prefix'].'.'.$type_1;//将上传格式改为图片的格式
			$file['local_target'] = FANWE_ROOT.$file['target'];
			$this->file = &$file;
			$this->error_code = 0;
			return true;
		}
	}

	/**
	 * 保存文件
	 * @return bool
	 */
	function save()
	{
		if(empty($this->file) || empty($this->file['tmp_name']))
			$this->error_code = -101;
		elseif(!$this->file['is_image'])
			$this->error_code = -102;
		elseif(!$this->saveFile($this->file['tmp_name'], $this->file['local_target'],$this->file['is_convert']))
			$this->error_code = -103;
		else
		{
			$this->error_code = 0;
			return true;
		}
		return false;
	}

	/**
	 * 获取错误代码
	 * @return number
	 */
	function error()
	{
		return $this->error_code;
	}

	/**
	 * 获取文件扩展名
	 * @return string
	 */
	function fileExt($file_name)
	{
		return addslashes(strtolower(substr(strrchr($file_name, '.'), 1, 10)));
	}

	/**
	 * 根据扩展名判断文件是否为图像
	 * @param string $ext 扩展名
	 * @return bool
	 */
	function isImageExt($ext)
	{
		static $img_ext  = array('jpg', 'jpeg', 'png', 'bmp','gif','giff');
		return in_array($ext, $img_ext) ? 1 : 0;
	}

	/**
	 * 获取图像信息
	 * @param string $target 文件路径
	 * @return mixed
	 */
	function getImageInfo($target)
	{
		static $infos = array();
		if(!isset($infos[$target]))
		{
			$infos[$target] = false;
			$ext = Image::fileExt($target);
			$is_image = Image::isImageExt($ext);

			if(!$is_image && $ext != 'tmp')
				return false;
			elseif(!is_readable($target))
				return false;
			elseif($image_info = @getimagesize($target))
			{
				$file_size = floatval(@filesize($target));
				$file_size = $file_size / 1024;
				if($file_size > $this->max_size)
					return false;
				
				list($width, $height, $type) = !empty($image_info) ? $image_info : array('', '', '');
				if($is_image && !in_array($type, array(1,2,3,6,13)))
					return false;

				$image_info['type'] = strtolower(substr(image_type_to_extension($image_info[2]),1));
				$infos[$target] = $image_info;
				return $image_info;
			}
			else
				return false;
		}
		return $infos[$target];
	}

	/**
	 * 获取是否充许上传文件
	 * @param string $source 文件路径
	 * @return bool
	 */
	function isUploadFile($source)
	{
		return $source && ($source != 'none') && (is_uploaded_file($source) || is_uploaded_file(str_replace('\\\\', '\\', $source)));
	}

	/**
	 * 获取保存的路径
	 * @param string $dir 指定的保存目录
	 * @return string
	 */
	function getTargetDir($dir)
	{
		if($dir == 'temp')
			$dir = './public/upload/temp/'.fToDate(NULL,'Y/m/d/H');
		else
			$dir = './public/upload/'.$dir.'/'.fToDate(NULL,'Y/m/d');

		makeDir(FANWE_ROOT.$dir);
		return $dir;
	}

	/**
	 * 保存文件
	 * @param string $source 源文件路径
	 * @param string $target 目录文件路径
	 * @return bool
	 */
	private function saveFile($source, $target,$is_convert = false)
	{
		if(!Image::isUploadFile($source))
			$succeed = false;
		elseif($is_convert && $this->convertType($source,$target))
			$succeed = true;
		elseif(@copy($source, $target))
			$succeed = true;
		elseif(function_exists('move_uploaded_file') && @move_uploaded_file($source, $target))
			$succeed = true;
		elseif (@is_readable($source) && (@$fp_s = fopen($source, 'rb')) && (@$fp_t = fopen($target, 'wb')))
		{
			while (!feof($fp_s))
			{
				$s = @fread($fp_s, 1024 * 512);
				@fwrite($fp_t, $s);
			}
			fclose($fp_s);
			fclose($fp_t);
			$succeed = true;
		}

		if($succeed)
		{
			$this->error_code = 0;
			@chmod($target, 0644);
			@unlink($source);
		}
		else
		{
			$this->error_code = 0;
		}

		return $succeed;
	}

	public function convertType($source,$target)
	{
		$info  = Image::getImageInfo($source);
        if($info !== false)
		{
			$width  = $info[0];
            $height = $info[1];
			$type = $info['type'];

			// 载入原图
			$createFun = 'imagecreatefrom'.($type=='jpg'?'jpeg':$type);
			if(!function_exists($createFun))
				$createFun = 'imagecreatefromjpeg';
			$srcImg = $createFun($source);

			if('gif'==$type || 'png'==$type)
				imagecolorallocate($srcImg,255,255,255);
			
			// 对jpeg图形设置隔行扫描
			if('jpg'==$type || 'jpeg'==$type)
				imageinterlace($srcImg,1);

			if($source == $target)
				@unlink($source);

			// 生成图片
			imagefilter($srcImg, IMG_FILTER_CONTRAST,-2);
			imagejpeg($srcImg,$target,IMAGE_CREATE_QUALITY);
			imagedestroy($srcImg);
			return true;
		}
		return false;
	}

	public function thumb($image,$maxWidth=200,$maxHeight=50,$gen = 0,$interlace=true,$filepath = '')
    {
        $info  =$this->get_image_info($image);
        if($info !== false)
		{
            $srcWidth  = $info['width'];
            $srcHeight = $info['height'];
           
			$type = $info['type'];
			if(empty($type)){
				return false;
			}

            $interlace  =  $interlace? 1:0;
            unset($info);

			if($maxWidth > 0 && $maxHeight > 0)
			{
				//$scale = min($maxWidth/$srcWidth, $maxHeight/$srcHeight); // 计算缩放比例
				//改为以宽度缩放
				$scale = $maxWidth/$srcWidth;
			}
			elseif($maxWidth == 0)
				$scale = $maxHeight/$srcHeight;
			elseif($maxHeight == 0)
				$scale = $maxWidth/$srcWidth;

            if($scale >= 1)
			{
                // 超过原图大小不再缩略
                $width   =  $srcWidth;
                $height  =  $srcHeight;
               
            }
			else
			{
                // 缩略图尺寸
                $width  = (int)($srcWidth*$scale);
                $height = (int)($srcHeight*$scale);
            }
           
           
			if($gen == 1)
			{
				$width = $maxWidth;
				$height = $maxHeight;
			}
			
			 

			$paths = pathinfo($image);
			$ext = $type;

			if(empty($filepath))
				$thumbname = str_replace('.'.$paths['extension'],'',$image).'_'.$maxWidth.'x'.$maxHeight.'.'.$ext;
			else
				$thumbname = $filepath;
			
			
			$thumburl = str_replace(FANWE_ROOT,'',$thumbname);

            // 载入原图
            $createFun = 'imagecreatefrom'.($type=='jpg'?'jpeg':$type);
           
			if(!function_exists($createFun))
				$createFun = 'imagecreatefromjpeg';

            $srcImg = $createFun($image);

            //创建缩略图
            if($type!='gif' && function_exists('imagecreatetruecolor'))
                $thumbImg = imagecreatetruecolor($width, $height);
            else
                $thumbImg = imagecreate($width, $height);

			$x = 0;
			$y = 0;

			if($gen == 1 && $maxWidth > 0 && $maxHeight > 0)
			{
				$resize_ratio = $maxWidth/$maxHeight;
				$src_ratio = $srcWidth/$srcHeight;
				if($src_ratio >= $resize_ratio)
				{
					$x = ($srcWidth - ($resize_ratio * $srcHeight)) / 2;
					$width = ($height * $srcWidth) / $srcHeight;
				}
				else
				{
					$y = ($srcHeight - ( (1 / $resize_ratio) * $srcWidth)) / 2;
					$height = ($width * $srcHeight) / $srcWidth;
				}
			}

            // 复制图片
            if(function_exists("imagecopyresampled"))
                imagecopyresampled($thumbImg, $srcImg, 0, 0, $x, $y, $width, $height, $srcWidth,$srcHeight);
            else
                imagecopyresized($thumbImg, $srcImg, 0, 0, $x, $y, $width, $height,  $srcWidth,$srcHeight);
            if('gif'==$type || 'png'==$type) {
                $background_color  =  imagecolorallocate($thumbImg,  0,255,0);  //  指派一个绿色
				imagecolortransparent($thumbImg,$background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
            }

            // 对jpeg图形设置隔行扫描
            if('jpg'==$type || 'jpeg'==$type)
				imageinterlace($thumbImg,$interlace);

            // 生成图片
			imagefilter($thumbImg, IMG_FILTER_CONTRAST,-1);

			// 保存图片
			$img_fuc="image".$type;
			if(!function_exists($img_fuc)){
				$img_fuc="imagejpeg";
			}
			$img_fuc($thumbImg,$thumbname);
			
            imagedestroy($thumbImg);
            imagedestroy($srcImg);
           
			return array('url'=>$thumburl,'path'=>$thumbname);
         }
         return false;
    }
    
	public function get_image_info($src){
		$image_info=getimagesize($src);
		
		$result=array();
		$result['width']=$image_info[0];
		$result['height']=$image_info[1];
		
		switch($image_info[2]){
			case 1:
			$result['type']='gif';
			break;
			case 2:
			$result['type']='jpeg';
			break;
			case 3:
			$result['type']='png';
			break;
			case 15:
			$result['type']='wbmp';
			break;
			case 16:
			$result['type']='xmb';
			break;
		}
		return $result;
		
	}
	public function water($source,$water,$alpha=80,$position="4")
    {
        //检查文件是否存在
        if(!file_exists($source)||!file_exists($water))
            return false;

        //图片信息
        $sInfo=$this->get_image_info($source);
        $wInfo=$this->get_image_info($water);
        //$sInfo = Image::getImageInfo($source);
        //$wInfo = Image::getImageInfo($water);

        //如果图片小于水印图片，不生成图片
        if($sInfo["width"] < $wInfo["width"] || $sInfo['height'] < $wInfo['height'])
            return false;
		if(empty($sInfo['type'])){
			return false;
		}
        //建立图像
      
		$sCreateFun="imagecreatefrom".$sInfo['type'];
		if(!function_exists($sCreateFun))
			$sCreateFun = 'imagecreatefromjpeg';
		$sImage=$sCreateFun($source);
	
        $wCreateFun="imagecreatefrom".$wInfo['type'];
		if(!function_exists($wCreateFun))
			$wCreateFun = 'imagecreatefromjpeg';
        $wImage=$wCreateFun($water);

        //设定图像的混色模式
       // imagealphablending($wImage, true);
		
         switch (intval($position))
        {
        	case 0: break;
        	//左上
        	case 1:
        		$posY=0;
		        $posX=0;
		        //生成混合图像
		       // imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['height'],$wInfo['width'],$alpha);
        		imagecopy($sImage, $wImage, $posX, $posY, 0, 0,$wInfo['width'],$wInfo['height']);
        		break;
        	//右上
        	case 2:
        		$posY=0;
		        $posX=$sInfo[0]-$wInfo['width'];
		        //生成混合图像
		        //imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'],$wInfo['height'],$alpha);
        		imagecopy($sImage, $wImage, $posX, $posY, 0, 0,$wInfo['width'],$wInfo['height']);
        		break;
        	//左下
        	case 3:
        		$posY=$sInfo['height']-$wInfo['height'];
		        $posX=0;
		        //生成混合图像
		        //imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'],$wInfo['height'],$alpha);
        		imagecopy($sImage, $wImage, $posX, $posY, 0, 0,$wInfo['width'],$wInfo['height']);
        		break;
        	//右下
        	case 4:
		        $posY=$sInfo['height']-$wInfo['height'];
		        $posX=$sInfo['width']-$wInfo['width'];
		        //生成混合图像
		       //imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0,$wInfo['width'],$wInfo['height'],$alpha);
        		 imagecopy($sImage, $wImage, $posX, $posY, 0, 0,$wInfo['width'],$wInfo['height']);
        		
        		break;
        	//居中
        	case 5:
		        $posY=$sInfo['height']/2-$wInfo['height']/2;
		        $posX=$sInfo['width']/2-$wInfo['width']/2;
		        //生成混合图像
		       // imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'],$wInfo['height'],$alpha);
        		imagecopy($sImage, $wImage, $posX, $posY, 0, 0,$wInfo['width'],$wInfo['height']);
        		break;
        }

        //如果没有给出保存文件名，默认为原图像名
        @unlink($source);
        //保存图像
        $imagefun="image".$sInfo['type'];
        if($sInfo['type']=='jpeg'){
        	
        	$imagefun($sImage,$source,IMAGE_CREATE_QUALITY);
        }else{
        	$imagefun($sImage,$source);
        }
		
        imagedestroy($sImage);
		imagedestroy($wImage);
    }
}

if(!function_exists('image_type_to_extension'))
{
	function image_type_to_extension($imagetype)
	{
		if(empty($imagetype))
			return false;

		switch($imagetype)
		{
			case IMAGETYPE_GIF    : return '.gif';
			case IMAGETYPE_JPEG   : return '.jpeg';
			case IMAGETYPE_PNG    : return '.png';
			case IMAGETYPE_SWF    : return '.swf';
			case IMAGETYPE_PSD    : return '.psd';
			case IMAGETYPE_BMP    : return '.bmp';
			case IMAGETYPE_TIFF_II : return '.tiff';
			case IMAGETYPE_TIFF_MM : return '.tiff';
			case IMAGETYPE_JPC    : return '.jpc';
			case IMAGETYPE_JP2    : return '.jp2';
			case IMAGETYPE_JPX    : return '.jpf';
			case IMAGETYPE_JB2    : return '.jb2';
			case IMAGETYPE_SWC    : return '.swc';
			case IMAGETYPE_IFF    : return '.aiff';
			case IMAGETYPE_WBMP   : return '.wbmp';
			case IMAGETYPE_XBM    : return '.xbm';
			default               : return false;
		}
	}
}
?>