import config
from datetime import datetime
from urllib.request import urlopen
import requests
import json
import os

import pathlib

watingJobs = []

def getNewJobs():
    response = urlopen(config.masterUrl + "/api/observation/record?key=" + config.apiKey)
    data_json = json.loads(response.read())
    return data_json

def apiSend(url, data, files=None):
    r = requests.post(url=config.masterUrl + url, data=data, files=files)
    return r.text

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

def setArtefacts(adir, observation):
    ufiles = {} # open('file.txt','rb')

    print("Uploading artefacts")

    for path, subdirs, files in os.walk(adir):
        for name in files:
            afile           = os.path.join(path, name)
            fileName        = str(afile).replace(str(adir), "").replace("/", "\\")
            print(fileName)
            ufiles[fileName] = open(afile, 'rb')


    apiSend("/api/observation/addArtefacts", {"id": observation}, ufiles)
    

def parseNewJobs(jobs):
    for job in jobs:
        job["start"] = datetime.strptime(job["start"], '%Y-%m-%d %H:%M:%S')
        job["end"]   = datetime.strptime(job["end"],   '%Y-%m-%d %H:%M:%S')

        if job["start"] < datetime.utcnow():
            setFail(job["id"])
            continue

        setAssigned(job["id"])

        watingJobs.append(job)

def pull():
    jobs = getNewJobs()
    parseNewJobs(jobs)