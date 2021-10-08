# Youtube-dl WebUI - QNAP Edition

## Description
This is a small web interface for youtube-dl command, specifically made for QNAP NAP.

## Features
- Preview available before download
- Download resolution limited to 480p
- Download run in background

## Requirements
- QNAP NAS with web server enabled and Python and PHP
- [Youtube-dl](https://github.com/rg3/youtube-dl)
- [FFmpeg](https://www.qnapclub.eu/en/qpkg/379)

## How to install?
### 1. Install youtube-dl
1. Download youtube-dl file the from the last release [here](https://github.com/ytdl-org/youtube-dl/releases/) 
2. Copy it in **/usr/bin** folder, give permissions with **chmod 777 youtube-dl**

### 2. Install ffmpeg
1. Download latest ffmpeg release [here](https://www.qnapclub.eu/en/qpkg/379) according your QNAP model
2. Install the **.qpkg** file manually with the **App Center**
3. You will find the new version of *ffmpeg* here: **/opt/ffmpeg**

### 3. Install webpages
1. Clone this repo in your web folder (ex: /share/Web/youtube-dl).
2. Edit config.php as you want it to work.
3. Create the video folder and put in the config.php (check also the permissions).
4. Access to your page (ie: http://(my_nas_IP)/youtube-dl/index.php) to check that everything works.

### 4. Configurations
1. Open config.php
2. Remove the proxy settings if you don't have
3. Set security to 1 to enable a the access with password
4. Find a password and hash it with md5 (you can do this with the md5.php page), then put the hash in the config file

## CSS Theme
[Flatly](http://bootswatch.com/flatly/)

## License
GPL v3 see LICENSE
