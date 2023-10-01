from pyorbital.orbital import Orbital
from datetime import datetime, timedelta
import time

import puller

def plan(lat, lon, alt, tle, transmitter, receiver, name, delta = timedelta(seconds=1800), predictH = 24, horizon = 0):
    #prevent plan same obsevation
    last = datetime.utcnow()

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

        if start < last:
            print(f"[INFO] alredy planed {name} at {start}")
            continue

        print(f"[INFO] planed {name} at {start}")

        puller.plan(transmitter, receiver, start, end)

def planAll(location):
    planeble = puller.getPlaneble()

    for transmitter in planeble:
        plan(
            location["lat"],
            location["lon"],
            location["alt"],
            transmitter["locator"]["tle"],
            transmitter["transmitter"],
            transmitter["receiver"],
            transmitter["name"]
        )