::Turn of displaying the code on the screen
@echo off
:: %1 is the psd to convert - example AFCCB071_s1.psd
:: %2 the event type - %event%  
:: %3 the name of the file before it was renamed - %oldfile%

IF "%2"=="Renamed" (
	move "F:\Shares\OFS\Images\ImageProcessing\ConvertedSimple\%~n3.jpg" "F:\Shares\OFS\Images\ImageProcessing\ConvertedSimpleRecycleBin\%~n3.jpg"
	move "F:\Shares\OFS\Images\ImageProcessing\OriginalSimple\%~n3.jpg" "F:\Shares\OFS\Images\ImageProcessing\OriginalSimpleRecycleBin\%~n3.jpg"
	del "F:\Shares\OFS\Website\Repo\Sync_Images\%~n3.jpg"
)
IF "%2"=="Deleted" (
	move "F:\Shares\OFS\Images\ImageProcessing\ConvertedSimple\%~n1.jpg" "F:\Shares\OFS\Images\ImageProcessing\ConvertedSimpleRecycleBin\%~n1.jpg"
	move "F:\Shares\OFS\Images\ImageProcessing\OriginalSimple\%~n1.jpg" "F:\Shares\OFS\Images\ImageProcessing\OriginalSimpleRecycleBin\%~n1.jpg"
	del "F:\Shares\OFS\Website\Repo\Sync_Images\%~n3.jpg"
	exit 0
)


:: Convert psd to jpg
php C:\Scripts\LiveScripts\Convert-ImagePSDtoJPG\Convert-ImagePSDtoJPG-Single.php -i "%~1" -o F:\Shares\OFS\Images\ImageProcessing\ConvertedSimple -p 1600 -l "F:\Shares\OFS\Images\ImageProcessing\Logs\Converted" -s -r
php C:\Scripts\LiveScripts\Convert-ImagePSDtoJPG\Convert-ImagePSDtoJPG-Single.php -i "%~1" -o F:\Shares\OFS\Images\ImageProcessing\OriginalSimple -p 1000 -l "F:\Shares\OFS\Images\ImageProcessing\Logs\Original" -s
php C:\Scripts\LiveScripts\Convert-ImagePSDtoJPG\Convert-ImagePSDtoJPG-Single.php -i "%~1" -o F:\Shares\OFS\Website\Repo\Sync_Images -p 1600 -l "F:\Shares\OFS\Images\ImageProcessing\Logs\Website" -s -r -w F:\Shares\OFS\Images\ImageProcessing\WebList

exit 0