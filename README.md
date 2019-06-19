# PHP-ImageMagickScripts
PHP ImageMagick Conversion Scripts to convert PSDs to JPGs

## Information
Used with https://directorymonitor.com/
Essentialy directory monitor would watch a folder, and on specific triggers call the script (create, edit, delete, rename)
After this the script would run and convert the specifc file from the PSD to JPG acording to the settings in the .bat file

## Sample call

```
php C:\Scripts\ConvertImageSingle\imageConverterSingle.php -i "F:\Shares\OFS\Images\MasterRepository\InputFile.psd" -o "F:\Shares\OFS\Images\ImageProcessing\ConvertedSimple" -p 1600 -l "F:\Shares\OFS\Images\ImageProcessing\Logs" -s
```