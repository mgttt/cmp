# Quick Tips: check out mkc and run

if cordova is init, you can run mkc by:
```
git clone https://github.com/SZU-BDI/app-mekongchat.git
cd proj-mkc-cordova/

cordova run android
or
cordova run ios
cordova run ios --list
cordova run ios --debug --emulator --target="iPhone-4s, 8.1"

```

# Quick Tips: Cordova install/init/update

@ref https://cordova.apache.org/#getstarted


install npm&node: https://docs.npmjs.com/getting-started/installing-node
```
wget https://nodejs.org/dist/v7.0.0/node-v7.0.0.pkg
```

npm update self
```
npm install -g npm
```

install cordova (from time to time), better using sudo...

```
# install to current !
# if want to install to current folder, remember to remove the node_modules at $HOME/.npm/ or %USERPROFILE%
npm install cordova . --save

# root mode (not recomment?)
sudo npm install -g cordova
```

create/init cordova project at current path
```
cordova create .
```

list platform
```
cordova platform ls
```

add platform
```
cordova platform add ios
cordova platform add android
cordova platform add browser

```

update platform
```
cordova platform update ios
cordova platform update android
```

add some plugin
```
cordova plugin add cordova-plugin-barcodescanner
cordova plugin add cordova-plugin-camera
```

run it ... (make sure XCODE/ANDROID-STUIDO is already installed !)
```
cordova run android
cordova run ios
```

## for windows cordova

```
wget https://nodejs.org/dist/v7.0.0/node-v7.0.0-x64.msi
node-v7.0.0-x64.msi

npm install cordova . --save

node_modules\cordova\bin\cordova.cmd platform ls
node_modules\cordova\bin\cordova.cmd platform add windows

```

other platform
```
ubuntu:
cordova platform add android
cordova platform add blackberry10
cordova platform add firefoxos
cordova platform add ubuntu

mac:
cordova platform add ios
cordova platform add amazon-fireos
cordova platform add android
cordova platform add blackberry10
cordova platform add firefoxos

win:
cordova platform add wp7
cordova platform add wp8
cordova platform add windows8
cordova platform add amazon-fireos
cordova platform add android
cordova platform add blackberry10
cordova platform add firefoxos
```

