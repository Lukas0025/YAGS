sudo systemctl stop yags

apt update
apt upgrade -y

# Install dependencies on Debian-based systems:
apt install -y python3 python3-pip
apt install -y git build-essential cmake g++ pkgconf libfftw3-dev libvolk2-dev libpng-dev                   # Core dependencies. If libvolk2-dev is not available, use libvolk1-dev
apt install -y libnng-dev                                                                                   # If this package is not found, follow build instructions below for NNG
apt install -y librtlsdr-dev libhackrf-dev libairspy-dev libairspyhf-dev                                    # All libraries required for live processing (optional)
apt install -y libglfw3-dev                                                                                 # Only if you want to build the GUI Version (optional)
apt install -y libzstd-dev                                                                                  # Only if you want to build with ZIQ Recording compression 

# If libnng-dev is not available, you will have to build it from source
git clone https://github.com/nanomsg/nng.git
cd nng
mkdir build && cd build
cmake -DCMAKE_BUILD_TYPE=Release -DBUILD_SHARED_LIBS=ON -DCMAKE_INSTALL_PREFIX=/usr ..
make
make install
cd ../..
rm -rf nng

# satdump
git clone https://github.com/altillimity/satdump.git
cd satdump
mkdir build && cd build
cmake -DCMAKE_BUILD_TYPE=Release -DBUILD_GUI=OFF -DCMAKE_INSTALL_PREFIX=/usr ..
make

make install

cd ../..
rm -rf satdump

# hamlib
apt install -y automake libtool

git clone https://github.com/Hamlib/Hamlib
cd Hamlib

./bootstrap
./configure --prefix=/usr/local --enable-static
make
make install

cd ..

rm -rf Hamlib

# sdrpp (for sdr server)
apt install -y libglfw3-dev libglew-dev libairspyhf-dev libiio-dev libad9361-dev libairspy-dev librtlsdr-dev portaudio19-dev libzstd1 libzstd-dev librtaudio-dev libsoapysdr-dev

git clone https://github.com/AlexandreRouma/SDRPlusPlus.git

cd SDRPlusPlus
mkdir build
cd build

cmake .. -DOPT_BUILD_NEW_PORTAUDIO_SINK:BOOL=ON

make -j4
make install
ldconfig

cd ../..
rm -rf SDRPlusPlus

## install YAGS client
cd station

sudo make install

#sudo chmod -R 777 /YAGS/records/
#sudo chmod -R 777 /YAGS/artefacts/

sudo systemctl enable yags
sudo systemctl start yags
