import config
import puller
import time
from datetime import datetime, timedelta
from recorder import recorder

def onRecorded(info):
    pass

while True:
    try:
        puller.pull()

        for job in puller.watingJobs:
            print(f"Job {job['target']['name']} starts at {job['start']}")

            if job["start"] <= datetime.utcnow() + timedelta(seconds=60):
                if job["end"] <= datetime.utcnow():
                    puller.setFail(job["id"])
                    puller.watingJobs.remove(job)

                # start record
                puller.setRecording(job["id"])

                curRecorder = recorder(job)
                curRecorder.start()
                
                puller.watingJobs.remove(job)
    
    except:
        print("[ERROR] main script fail restarting")




    time.sleep(config.pullInterval)