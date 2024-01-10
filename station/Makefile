install:
	apt install -f libopenblas-dev
	apt install -f imagemagick

	pip3 install matplotlib
	pip3 install numpy
	pip3 install pyorbital
	pip3 install pyserial

	cp tools/baseband_spectogram.py /usr/local/bin/
	chmod +x /usr/local/bin/baseband_spectogram.py

	cp tools/cw_morse.py /usr/local/bin/
	chmod +x /usr/local/bin/cw_morse.py

	useradd yags || echo "user yags already exists SKIP"
	mkdir /home/yags/ || echo "home for user yags already exists SKIP"
	adduser yags dialout || echo "user yags already in dialout SKIP"
	adduser yags plugdev || echo "user yags already in plugdev SKIP"

	rm -rf /YAGS
	mkdir /YAGS
	sudo cp -r * /YAGS/
	cp -f yags.service /etc/systemd/system/

	chown -R yags:yags /YAGS/
	chown -R yags:yags /home/yags/

	systemctl daemon-reload

