<?php
//php C:\Scripts\ConvertImageSingle\imageConverterSingle.php -i "F:\Shares\OFS\Images\MasterRepository\InputFile.psd" -o "F:\Shares\OFS\Images\ImageProcessing\ConvertedSimple" -p 1600 -l "F:\Shares\OFS\Images\ImageProcessing\Logs" -s -r -w "O:\Images\ImageProcessing\WebList"

$shortopts  = "";
$shortopts .= "i:";		//input file
$shortopts .= "o:"; 	//output folder
$shortopts .= "p:"; 	//pixel size
$shortopts .= "l:"; 	//log folder
$shortopts .= "w:";		//Option to check if image should be processed for the web
$shortopts .= "s::";	//Option to check if image should be square or not
$shortopts .= "r::";	//Option to check if image should be resized or not

$options = getopt($shortopts);

$input_file = $options[i];
$output_folder = $options[o];
$pixel_size = (int) $options[p];
$log_folder = $options[l];
$square_image = $options[s];
$resize_image = $options[r];
$web_list_folder = $options[w];

$fileName = pathinfo(__FILE__, PATHINFO_FILENAME);

$opDateTime = date("Y-m-d_H-i-s");


if (is_int($pixel_size) == FALSE)
{
	//pixel_size isnt an valid number
	logMessage("pixel_size does not exist"."\t".$pixel_size);
}
elseif (file_exists($input_file) == FALSE)
{
	//input_file doesnt exist
	logMessage("input_file does not exist"."\t".$input_file);
} 
elseif (file_exists($output_folder) == FALSE) 
{
	//output_folder doesnt exist
	logMessage("output_folder does not exist"."\t".$output_folder);
} 
elseif (file_exists($log_folder) == FALSE) 
{
	//log_folder doesnt exist
	logMessage("log_folder does not exist"."\t".$log_folder);
} 
else 
{
	//execute script
	$start_time_conversion_image = microtime(true);
	$name = basename($input_file, ".psd");

	$img = new Imagick();
	$img->readImage($input_file."[0]");
	$img->setImageAlphaChannel(imagick::ALPHACHANNEL_OPAQUE);
	$img->setImageBackgroundColor('white');
	$img->setImageFormat('jpg');
	$img->flattenimages();
	
	$imgWidth = $img->getImageWidth();
	$imgHeight = $img->getImageHeight();

	$borderWidth = $imgWidth*0.02;
	$borderHeight = $imgHeight*0.02;

	$calcBorder = max($borderWidth, $borderHeight);

	$borderWidth = max($calcBorder,5);
	$borderHeight = max($calcBorder,5);

	$img->borderImage('#FFFFFF',$borderWidth, $borderHeight);
	$img->stripImage();

	//Recalculating the image now as we want to scale to 1600 or less - if we do this with the old image size - some images end up greater than 1600 because of the added borders not being taken into account.

	$imgWidth = $img->getImageWidth();
	$imgHeight = $img->getImageHeight();
	

	//Check if resize flag is enabled
	if (isset($resize_image)){
		//YES RESIZE THE IMAGE (resize the image to the scaled size)

		//Check if image is larger than requested size - if so scale it down
		if ($imgWidth > $pixel_size || $imgHeight > $pixel_size) {
			$img->scaleImage($pixel_size, $pixel_size, true);
			$output_size = $pixel_size;
		} 
		else 
		{
			$output_size = max($imgWidth, $imgHeight);
		}
		$resize_output = "TRUE";
	}
	else
	{
		//NO DONT RESIZE THE IMAGE (unless it is smaller than the pixel size, if so scale it up to pixel size)
		if ($imgWidth < $pixel_size || $imgHeight < $pixel_size) {
			$img->scaleImage($pixel_size, $pixel_size, true);
			$output_size = $pixel_size;
		} 
		else 
		{
			$output_size = max($imgWidth, $imgHeight);
		}
		$resize_output = "FALSE";
	}
	
	//Check if flag is enabled
	if (isset($square_image)){
		//This squares the image
		$img->extentImage($output_size,$output_size,($img->getImageWidth()-$output_size)/2,($img->getImageHeight()-$output_size)/2);
		$sqaure_output = "TRUE";
	}
	else
	{
		$sqaure_output = "FALSE";
	}
		
	//Check if weblist folder is set or not
	//if not set just process normaly
	if (isset($web_list_folder)){
		logMessage("web process enabled");
		echo($web_list_folder);
		if (file_exists($web_list_folder) == FALSE)
		{
			//web list doesnt exist
			logMessage("web list folder does not exist"."\t".$web_list_folder);
			exit();
		}
		else
		{
			//Web Enabled check.
			//For comparison remove the suffix down to just the SKU
			$needle = substr($name, 0, strpos($name, "_"));
			//New list each day
			$webList = $web_list_folder."\\webList-".date("Y-m-d").".txt";
			$webListHaystack = file_get_contents($webList);
			logMessage($needle);	
			//WebList check - use preg match instead of exploding into an array and in_array - tests indicated this was faster
			if (preg_match("/\b".$needle."\b/i", $webListHaystack)) {
				$img->setImageCompression(Imagick::COMPRESSION_JPEG2000);
				$img->setImageCompressionQuality(72);
				$img->writeImage($output_folder."\\".$name.".jpg");
				logMessage("image found in the product web simple");	
			} else {
				logMessage("image not found in product web simple");
				exit();
			}
		}
	}
	else
	{
		$img->setImageCompression(Imagick::COMPRESSION_JPEG2000);
		$img->setImageCompressionQuality(72);
		$img->writeImage($output_folder."\\".$name.".jpg");
	}


	$end_time_conversion_image = microtime(true);

	//Datetime for the entry of the log
	$imgConTime = bcsub($end_time_conversion_image, $start_time_conversion_image,4);	
	logConversion($name."\t".$output_folder."\\".$name.".jpg"."\t".$pixel_size."\t".$output_size."\t".$sqaure_output."\t".$resize_output."\t".isset($web_list_folder)."\t".$imgConTime);
}

//The repition of code is too much to look at so simple log function
function logMessage($logmessage_append) {
	global $fileName, $log_folder;
	$logFile = $log_folder."\\".$fileName."-Messages_".date("Y-M").".log";
	$logmessage_base = "[".date("Y-m-d H:i:s")."]\t";
	$logmessage_post = $logmessage_base.$logmessage_append."\r\n";
	file_put_contents($logFile, $logmessage_post, FILE_APPEND | LOCK_EX);
}

function logConversion($logmessage_append) {
	global $fileName, $log_folder;
	$logFile = $log_folder."\\".$fileName."-Conversions_".date("Y-M").".log";
	$logmessage_base = "[".date("Y-m-d H:i:s")."]\t";
	$logmessage_post = $logmessage_base.$logmessage_append."\r\n";
	file_put_contents($logFile, $logmessage_post, FILE_APPEND | LOCK_EX);
}
exit(); //Just for you.
?>