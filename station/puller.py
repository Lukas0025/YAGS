import config
from datetime import datetime
from urllib.request import urlopen
import requests
import json
import os
import pathlib
from loguru import logger

watingJobs = []
location   = {}

def getNewJobs():
    response = urlopen(config.masterUrl + "/api/observation/record?key=" + config.apiKey)
    data_json = json.loads(response.read())
    return data_json

def getInfo():
    response = urlopen(config.masterUrl + "/api/station/APIinfo?key=" + config.apiKey)
    data_json = json.loads(response.read())
    return data_json

def getPlaneble():
    response = urlopen(config.masterUrl + "/api/station/autoPlanable?key=" + config.apiKey)
    data_json = json.loads(response.read())
    return data_json

def apiSend(url, data, files=None):
    try:
        r = requests.post(url=config.masterUrl + url, data=data, files=files, timeout=10)
    except requests.Timeout:
        logger.error("Api send fail timeout {}".format(config.masterUrl + url))
        return None
    except requests.ConnectionError:
        logger.error("Api send fail connection error {}".format(config.masterUrl + url))
        return None

    return r.text

def plan(transmitter, receiver, start, end):
    apiSend("/api/observation/plan", {
        "transmitter": transmitter,
        "receiver":    receiver,
        "start":       start.strftime("%Y-%m-%dT%H:%M:%S"),
        "end":         end  .strftime("%Y-%m-%dT%H:%M:%S")
    })

def setFail(observation):
    apiSend("/api/observation/fail", {"id": observation})

def setAssigned(observation):
    apiSend("/api/observation/assigned", {"id": observation})

def setRecording(observation):
    apiSend("/api/observation/recording", {"id": observation})

def setRecorded(observation):
    apiSend("/api/observation/recorded", {"id": observation})

def setDecoding(observation):
    apiSend("/api/observation/decoding", {"id": observation})

def setSuccess(observation):
    apiSend("/api/observation/success", {"id": observation})

def read_in_chunks(file_object, chunk_size=None):

    if chunk_size is None:
        chunk_size = config.MaxUploadChunk

    while True:
        data = file_object.read(chunk_size)
        if not data:
            break
        yield data

def setArtefacts(adir, observation):
    logger.debug("Uploading artefacts")

    for path, subdirs, files in os.walk(adir):
        for name in files:
            afile           = os.path.join(path, name)
            fileName        = str(afile).replace(str(adir), "").replace("/", "\\")
            aPath           = str(path).replace(str(adir), "").replace("/", "\\")
            ufile           = open(afile, 'rb')

            index = 0
            offset = 0

            for chunk in read_in_chunks(ufile):
                offset = index + len(chunk)

                logger.debug(f"Sending file {fileName} chunk with offset {index}")
                if apiSend("/api/observation/addArtefacts", {
                    "id":     observation,
                    "fname":  name,
                    "path":   aPath,
                    "offset": index,
                    "data":   chunk
                }) is None:
                    logger.error(f"Sending file {fileName} fail in chunk with offset {index}")
                    break


                index = offset


    return True
    

def parseNewJobs(jobs):
    for job in jobs:
        job["start"] = datetime.strptime(job["start"], '%Y-%m-%d %H:%M:%S')
        job["end"]   = datetime.strptime(job["end"],   '%Y-%m-%d %H:%M:%S')

        if job["start"] < datetime.utcnow():
            setFail(job["id"])
            continue

        setAssigned(job["id"])

        watingJobs.append(job)

def parseInfo(info):
    if "gps" in info["locator"]:
        location["lat"] = info["locator"]["gps"]["lat"]
        location["lon"] = info["locator"]["gps"]["lon"]
        location["alt"] = info["locator"]["gps"]["alt"] / 1000

        #print(f"[INFO] loaded locator from YAGS server LAT: {position["lat"]}, LON: {position["lon"]}, ALT: {position["alt"]}")

def pull():
    #get station info
    info = getInfo()
    parseInfo(info)
    jobs = getNewJobs()
    parseNewJobs(jobs)
