import config
import puller
import time
from datetime import datetime, timedelta
from recorder import recorder

import sys
import traceback
import planner
import threading

from loguru import logger

assigned = threading.Lock()
#assigned.release()

i = 0

while True:
    try:
        if (i % config.pullInterval) == 0:
            puller.pull()

        jobsDeltas = []
        for job in puller.watingJobs:

            jobsDeltas.append((job["start"] - datetime.utcnow()).total_seconds())
            
            if job["start"] <= datetime.utcnow() + timedelta(seconds=60):
                if job["end"] <= datetime.utcnow() or not assigned.acquire(timeout=10):
                    puller.setFail(job["id"])
                    puller.watingJobs.remove(job)
                    logger.debug("Canceling job {} because is ended lock state is {}".format(job["id"], assigned.locked()))
                    break

                logger.debug("I have lock")

                # start record
                puller.setRecording(job["id"])

                logger.debug("Starting record process for job {}".format(job["id"]))
                curRecorder = recorder(job, puller.location, assigned)
                curRecorder.start()

                puller.watingJobs.remove(job)

        # ask for planeble satellites
        if (i % config.planInterval) == 0:
            logger.debug("Calling planner")
            planner.planAll(puller.location)

        i += 1
    
    except Exception as inst:
        logger.error(f"main script fail restarting - error {inst}")

        # Get current system exception
        ex_type, ex_value, ex_traceback = sys.exc_info()

        # Extract unformatter stack traces as tuples
        trace_back = traceback.extract_tb(ex_traceback)

        # Format stacktrace
        stack_trace = list()

        for trace in trace_back:
            stack_trace.append("File : %s , Line : %d, Func.Name : %s, Message : %s" % (trace[0], trace[1], trace[2], trace[3]))

        logger.error("Exception type : %s " % ex_type.__name__)
        logger.error("Exception message : %s" %ex_value)
        logger.error("Stack trace : %s" %stack_trace)

    time.sleep(1)
