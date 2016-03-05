

#@ref https://github.com/docker/docker/issues/8710

===================================
#install X11(XQuartz) in macos
brew cask install xquartz

#socat: to link X11 (from xquartz) to local port
brew install socat
open -a XQuartz

#start the link
socat TCP-LISTEN:6000,reuseaddr,fork UNIX-CLIENT:\"$DISPLAY\"

====================================
#in docker 
MYIP=`ifconfig ${NET_IF} | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*'
 | grep -v '127.0.0.1' | head -1`
echo $MYIPdocker run -e DISPLAY=$MYIP:0 -v yourapp.nw:/opt/nwjs/package.nw kxes/nwjs
