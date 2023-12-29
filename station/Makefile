install:
	apt install -f libopenblas-dev
	pip3 install matplotlib
	pip3 install numpy
	pip3 install pyorbital
	pip3 install pyserial

	cp tools/baseband_spectogram.py /usr/local/bin/
	chmod +x /usr/local/bin/baseband_spectogram.py

	cp tools/cw_morse.py /usr/local/bin/
	chmod +x /usr/local/bin/cw_morse.py

	rm -rf /YAGS
	mkdir /YAGS
	sudo cp -r * /YAGS/
	cp -f yags.service /etc/systemd/system/
        systemctl daemon-reload
