from pyorbital.orbital import Orbital
from datetime import datetime, timedelta
import time
from operator import itemgetter

import puller

from loguru import logger

def plan(lat, lon, alt, tle, transmitter, receiver, priority, name, delta = timedelta(seconds=1800), predictH = 12, horizon = 5):
    #prevent plan same obsevation
    last = datetime.utcnow()
    plans = []

    for ob in puller.watingJobs:
        last = max(ob["end"], last)

    orb = Orbital(name, line1=tle["line1"], line2=tle["line2"])

    passes = orb.get_next_passes(
        datetime.utcnow() + delta,
        predictH,
        lon,
        lat,
        alt,
        tol = 0.001,
        horizon = horizon
    )

    for ob in passes:
        start = ob[0]
        end   = ob[1]

        if start <= (last + timedelta(seconds=60)): # must be minute after last
            #logger.debug(f"alredy planed {name} at {start} skiping")
            continue

        plans.append({
            "transmitter": transmitter,
            "receiver":    receiver,
            "start":       start,
            "end":         end,
            "priority":    priority
        })

        logger.debug(f"planed {name} at {start}")

    return plans

def planAll(location):
    planeble = puller.getPlaneble()
    plans    = []

    for transmitter in planeble:
        plans += plan(
            location["lat"],
            location["lon"],
            location["alt"],
            transmitter["locator"]["tle"],
            transmitter["transmitter"],
            transmitter["receiver"],
            transmitter["priority"],
            transmitter["name"]
        )

    plans = sorted(plans, key=itemgetter('start')) 

    i = 0
    while i + 1 < len(plans):
        if plans[i]["end"] < plans[i + 1]["start"]:
            puller.plan(plans[i]["transmitter"], plans[i]["receiver"], plans[i]["start"], plans[i]["end"])
            i += 1
        
        elif plans[i]["priority"] > plans[i + 1]["priority"]:
            plans.pop(i + 1)
        else:
            i += 1
