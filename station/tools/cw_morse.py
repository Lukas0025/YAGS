#!/usr/bin/env python
# Simple spectogram ploter

import argparse
import numpy as np
import matplotlib.pyplot as plt

if __name__ == '__main__':
    cliParser = argparse.ArgumentParser(description='Simple spectogram ploter')    
    
    cliParser.add_argument('input_file',  type=str, help='input filename baseband record')
    cliParser.add_argument('output_file', type=str, help='output filename decoded text')
    cliParser.add_argument('-fs',  '--sampleRate', type=float, help='sets the sample rate [hz]')    
    cliParser.add_argument('-mfs', '--morseFS',    type=float, help='sets the sample rate for CW (Downsample) [hz]', default=125000)    
    cliParser.add_argument('-of',  '--offsetFreq', type=float, help='sets carrier offset [hz]', default=0)   
    cliParser.add_argument('-bw',  '--bandWidth', type=float, help='sets carrier bandwidth [hz]', default=60)
    cliParser.add_argument('-fc',  '--failChar', type=str, help='sets char what is used when morse decode fail', default='')

        
    cliParser.add_argument('-f', '--format', type=str, 
        help='Output format', 
        choices=["int8"],
        default='int8')

    args = cliParser.parse_args()
    
    data = np.memmap(args.input_file, dtype=args.format, mode="r")
    
    fft_size    = 1024
        
    sample_rate   = args.sampleRate
    down_rate     = args.morseFS
    offset_freq   = args.offsetFreq
    bandwidth     = args.bandWidth
    failChar      = args.failChar
    
    downSample    = int(sample_rate // down_rate) 
    sampleSize    = 2
    
    num_rows      = len(data) // (fft_size * sampleSize * downSample)
    num_samples   = len(data) // (sampleSize * downSample)
    
    DC_PART = 0
    for i in range(num_rows):
        subdata_start = i * sampleSize * fft_size * downSample
        subdata = data[subdata_start : subdata_start + fft_size * sampleSize * downSample]
        
        subdata = subdata[1::2] + 1j * subdata[0::2] # convert to complex
        subdata = subdata[0::downSample]             # downsample
        DC_PART += np.sum(subdata)

    DC_PART /= num_samples
    
    print(f"DC part is {DC_PART}")
    
    beep_trashold = 0
    
    for i in range(num_rows):
        subdata_start = i * sampleSize * fft_size * downSample
        subdata = data[subdata_start : subdata_start + fft_size * sampleSize * downSample]
        
        subdata = subdata[1::2] + 1j * subdata[0::2] # convert to complex
        subdata = subdata[0::downSample]             # downsample
        subdata -= DC_PART
        
        fft       = np.abs(np.fft.fftshift(np.fft.fft(subdata)))
        frame_max = np.max(fft[len(fft)//2 - bandwidth//2 + offset_freq : len(fft)//2 +  bandwidth//2 + offset_freq])
        beep_trashold += frame_max
        
    beep_trashold /= num_rows
    
    print(f"beep trashold is {beep_trashold}")

    
    is_one        = False
    mean_one_len  = []
    mean_zero_len = []
    sig_len       = 0

    for i in range(num_rows):
        subdata_start = i * sampleSize * fft_size * downSample
        subdata = data[subdata_start : subdata_start + fft_size * sampleSize * downSample]
        
        subdata = subdata[1::2] + 1j * subdata[0::2] # convert to complex
        subdata = subdata[0::downSample]             # downsample
        subdata -= DC_PART
        
        fft       = np.abs(np.fft.fftshift(np.fft.fft(subdata)))
        frame_max = np.max(fft[len(fft)//2 - bandwidth//2 + offset_freq : len(fft)//2 +  bandwidth//2 + offset_freq])
        
        if (frame_max >= beep_trashold) and not(is_one):
            mean_zero_len.append(sig_len)
            sig_len       = 0
            is_one        = True
        
        if (frame_max < beep_trashold) and is_one:
            mean_one_len.append(sig_len)
            sig_len       = 0
            is_one        = False
        
        sig_len += 1
    
    mean_one_len  = np.mean(mean_one_len)
    mean_zero_len = np.mean(mean_zero_len)
    
    is_one   = False
    morse    = ""
    sig_len  = 0
    
    for i in range(num_rows):
        subdata_start = i * sampleSize * fft_size * downSample
        subdata = data[subdata_start : subdata_start + fft_size * sampleSize * downSample]
        
        subdata = subdata[1::2] + 1j * subdata[0::2] # convert to complex
        subdata = subdata[0::downSample]             # downsample
        subdata -= DC_PART
        
        fft       = np.abs(np.fft.fftshift(np.fft.fft(subdata)))
        frame_max = np.max(fft[len(fft)//2 - bandwidth//2 + offset_freq : len(fft)//2 +  bandwidth//2 + offset_freq])
        
        if (frame_max >= beep_trashold) and not(is_one):
            is_one        = True
            if sig_len >= mean_zero_len * 2:
                morse += "\n"
            if sig_len >= mean_zero_len:
                morse += " "

            sig_len = 0
        
        if (frame_max < beep_trashold) and is_one:
            is_one        = False
            if sig_len > mean_one_len:
                morse += "-"
            else:
                morse += "."
            sig_len = 0
        
        sig_len += 1


    # extra space added at the end to access the
    # last morse code
    MORSE_CODE_DICT = { 'A':'.-', 'B':'-...',
                    'C':'-.-.', 'D':'-..', 'E':'.',
                    'F':'..-.', 'G':'--.', 'H':'....',
                    'I':'..', 'J':'.---', 'K':'-.-',
                    'L':'.-..', 'M':'--', 'N':'-.',
                    'O':'---', 'P':'.--.', 'Q':'--.-',
                    'R':'.-.', 'S':'...', 'T':'-',
                    'U':'..-', 'V':'...-', 'W':'.--',
                    'X':'-..-', 'Y':'-.--', 'Z':'--..',
                    '1':'.----', '2':'..---', '3':'...--',
                    '4':'....-', '5':'.....', '6':'-....',
                    '7':'--...', '8':'---..', '9':'----.',
                    '0':'-----', ', ':'--..--', '.':'.-.-.-',
                    '?':'..--..', '/':'-..-.', '-':'-....-',
                    '(':'-.--.', ')':'-.--.-'}
    
    message = morse + ' '
 
    decipher = ''
    citext = ''
    for letter in message:
        if (not(letter in [' ', '\n'])): 
            citext += letter
        else:        
            if (citext in MORSE_CODE_DICT.values()):
                decipher += list(MORSE_CODE_DICT.keys())[list(MORSE_CODE_DICT.values()).index(citext)]
            elif citext != '':
                decipher += failChar
        
            if (letter == '\n'): 
                decipher += ' '    
        
            citext = ''
            
    f = open(args.output_file, "w+")
    f.write(decipher)
    f.close()