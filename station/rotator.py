import threading
from pyorbital.orbital import Orbital
from datetime import datetime, timedelta
import time

class rotator(threading.Thread):
    def __init__(self, driver, job, station):
        threading.Thread.__init__(self)
        self.driver  = driver
        self.job = job
        self.station = station
        self.killed = False
 
    def run(self):
        print("[INFO] Starting rotator service")

        self.driver.reset()
        time.sleep(30)

        #init pyorbytal
        orb = Orbital(self.job["target"]["name"], line1=self.job["target"]["locator"]["tle"]["line1"], line2=self.job["target"]["locator"]["tle"]["line2"])

        while (True):
            az, el = orb.get_observer_look(
                utc_time=datetime.utcnow() + timedelta(seconds=5),
                lon=self.station["lon"],
                lat=self.station["lat"],
                alt=self.station["alt"]
            )
            az, el = round(az), round(el)
            
            print(f"[INFO] rotator az: {az}, el: {el}")

            self.driver.set_azel(az, el)

            if (self.killed):
                break
            
            time.sleep(10)

        # home the rotator on end
        self.driver.reset()

        time.sleep(60)

            
    
    def kill(self):
        self.killed = True