##
# Config of upstream YAGS server
#
masterUrl      = "http://10.0.0.8"
apiKey         = "d0ec2b81-601b-481a-bde9-4e6699fd9297"

##
# Intervals for pulling (getting from YAGS server) and palning
#
pullInterval   = 120  # in sec
planInterval   = 1200 # in sec

##
# Chunk upload config
#
MaxUploadChunk = 5000000 # in bytes

##
# Compression
#
# compress convert all png to jpg and compress all .s8 to .tar.xz
#
compress       = True # compress artifacts
compressJpgQ   = 70 # quality of compresses JPG
