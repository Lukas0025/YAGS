import serial
import time

class simplecom(object):
    def __init__(self, port):
        self.port   = port
        self.serial = serial.Serial(self.port, 9600, timeout=60)

    def send(self, cmd):
        try:
            self.serial.write(cmd.encode("ASCII"))
            self.serial.flush()
        except:
            print("[ERROR] fail to write to serial")

    def reset(self):
        self.send("RESET\n")

    def set_azel(self, az, el):
        self.set_az(az)
        self.set_el(el)

    def set_az(self, az):
        while (az < 0):
            az += 360

        az = round(az % 360)

        self.send(f"AZ{az}\n")
        #readout target
        self.send(f"TAR\n")

    def set_el(self, el):
        if  (el < 0):
            el = 0
        elif (el > 90):
            el = 90

        el = round(el)

        self.send(f"EL{el}\n")
        #readout target
        self.send(f"TAR\n")