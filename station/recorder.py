import os
import puller
import threading
from rotator import rotator
from simplecom import simplecom
from pathlib import Path
import config
import time
import datetime

from loguru import logger

# A recursive function to remove the folder
def del_folder(path):
    for sub in path.iterdir():
        if sub.is_dir():
            # Delete directory if it's a subdirectory
            del_folder(sub)
        else :
            # Delete file if it is a file:
            sub.unlink()
    
    # This removes the top-level folder:
    path.rmdir()

class recorder(threading.Thread):
    def __init__(self, job, location, lock):
        threading.Thread.__init__(self)
        self.job = job
        self.location = location
        self.lock = lock
 
    def run(self):
        logger.debug(f"Recorder for job {self.job['target']['name']} started")

        recordTime = (self.job["end"] - self.job["start"]).total_seconds()

        #init rotator
        rotatorDriver = simplecom("/dev/ttyUSB0")
        rotatorCTR    = rotator(rotatorDriver, self.job, self.location)
        rotatorCTR.start()

        baseband = f"records/{self.job['id']}"
        
        fs = max(self.job["receiver"]["params"]["fs"])

        # find supported FS
        for sample in self.job["receiver"]["params"]["fs"]:
            if (sample > int(self.job['transmitter']['bandwidth'])) and (sample < fs):
                fs = sample

        time.sleep(50)

        realStart = int(datetime.datetime.now(tz=datetime.timezone.utc).timestamp())

        ret = os.system(f"satdump record {baseband} --source {self.job['receiver']['params']['radio']} --samplerate {fs} --frequency {self.job['transmitter']['centerFrequency']} --gain {self.job['receiver']['params']['gain']} --baseband_format s8 --timeout {recordTime}")

        rotatorCTR.kill()
        self.lock.release()

        logger.debug("Release lock")

        if ret != 0: # fail to open sdr
            puller.setFail(self.job["id"])
            return

        realEnd   = int(datetime.datetime.now(tz=datetime.timezone.utc).timestamp())

        print(f"Recorder for job {self.job['target']['name']} stoped")

        puller.setRecorded(self.job["id"])
        puller.setDecoding(self.job["id"])

        #create artecats dir
        adir = f"artefacts/{self.job['id']}"
        os.makedirs(adir)

        replacements = {
            "{baseband}":    str(baseband) + ".s8",
            "{fs}":          str(fs),
            "{artefactDir}": str(adir),
            "{freq}":        str(self.job['transmitter']['centerFrequency']),
            "{targetNum}":   ''.join(x for x in self.job['target']['name'] if x.isdigit()),
            "{target}":      self.job['target']['name'],
            "{start}":       str(realStart),
            "{end}":         str(realEnd)     
        }

        for pipe in self.job["proccessPipe"]:
            #ok now replace 
            for k, v in replacements.items():
                pipe = pipe.replace(k, v)

            ret = os.system(pipe)

            if ret != 0:
                logger.error("Process pipe {} fail".format(pipe))

        puller.setSuccess(self.job["id"])

        logger.debug("Starting upload of artifacts")

        if not puller.setArtefacts(adir, self.job["id"]):
            puller.setFail(self.job["id"])

        # remove basband record
        os.remove(str(baseband) + ".s8")

        # remove artefacts
        path = Path(adir)
        try:
            del_folder(path)
            logger.debug("Directory with artifacts removed successfully")
        except OSError as o:
            logger.debug(f"Error, {o.strerror}: {path}")

        logger.debug("Recored done")
