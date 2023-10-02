from pyorbital.orbital import Orbital
from datetime import datetime, timedelta
import time
from operator import itemgetter

import puller

def plan(lat, lon, alt, tle, transmitter, receiver, priority, name, delta = timedelta(seconds=1800), predictH = 12, horizon = 5):
    #prevent plan same obsevation
    last = datetime.utcnow()
    plans = []

    for ob in puller.watingJobs:
        last = max(ob["start"], last)

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

        if start <= last:
            print(f"[INFO] alredy planed {name} at {start}")
            continue

        plans.append({
            "transmitter": transmitter,
            "receiver":    receiver,
            "start":       start,
            "end":         end,
            "priority":    priority
        })

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
            transmitter["priority"]
            transmitter["name"]
        )

    plans = sorted(plans, key=itemgetter('start')) 

    i = 0
    while i + 1 < len(plans):
        if plan[i]["end"] < plan[i + 1]["start"]:
            puller.plan(plan[i]["transmitter"], plan[i]["receiver"], plan[i]["start"], plan[i]["end"])
            i += 1
        
        elif plan[i]["priority"] > plan[i + 1]["priority"]:
            plan.pop(i + 1)
        else:
            i += 1