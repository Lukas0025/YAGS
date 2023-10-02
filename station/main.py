import config
import puller
import time
from datetime import datetime, timedelta
from recorder import recorder

import sys
import traceback
import planner

def onRecorded(info):
    pass

i = 0
while True:
    try:
        if (i % config.pullInterval) == 0:
            puller.pull()

        jobsDeltas = []
        for job in puller.watingJobs:

            jobsDeltas.append((job["start"] - datetime.utcnow()).total_seconds())
            
            if job["start"] <= datetime.utcnow() + timedelta(seconds=60):
                if job["end"] <= datetime.utcnow():
                    puller.setFail(job["id"])
                    puller.watingJobs.remove(job)

                # start record
                puller.setRecording(job["id"])

                curRecorder = recorder(job, puller.location)
                curRecorder.start()
                
                puller.watingJobs.remove(job)

        if (i % 10) == 0 and len(jobsDeltas):
            print(f"Next job in {min(jobsDeltas)}s")

        # ask for planeble satellites
        if (i % config.planInterval) == 0:
            planner.planAll(puller.location)

        i += 1
    
    except Exception as inst:
        print(f"[ERROR] main script fail restarting - error {inst}")

        # Get current system exception
        ex_type, ex_value, ex_traceback = sys.exc_info()

        # Extract unformatter stack traces as tuples
        trace_back = traceback.extract_tb(ex_traceback)

        # Format stacktrace
        stack_trace = list()

        for trace in trace_back:
            stack_trace.append("File : %s , Line : %d, Func.Name : %s, Message : %s" % (trace[0], trace[1], trace[2], trace[3]))

        print("Exception type : %s " % ex_type.__name__)
        print("Exception message : %s" %ex_value)
        print("Stack trace : %s" %stack_trace)

    time.sleep(1)