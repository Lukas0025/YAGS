#!/usr/bin/env python
# Simple spectogram ploter

import argparse
import numpy as np
import matplotlib.pyplot as plt

if __name__ == '__main__':
    cliParser = argparse.ArgumentParser(description='Simple spectogram ploter')    
    
    cliParser.add_argument('input_file',  type=str, help='input filename')
    cliParser.add_argument('output_file', type=str, help='output filename')
    cliParser.add_argument('-fs', '--sampleRate', type=float, help='sets the sample rate [hz]')    
    cliParser.add_argument('-fc', '--centralFreq', type=float, help='sets center freq of spectrum [hz]')    
    
    cliParser.add_argument('-f', '--format', type=str, 
        help='Output format', 
        choices=["int8"],
        default='int8')

    args = cliParser.parse_args()
    
    data = np.memmap(args.input_file, dtype=args.format, mode="r")
    
    fft_size    = 1024
    sampleSize  = 2 # I and Q
        
    sample_rate   = args.sampleRate
    Fc            = args.centralFreq
    num_rows      = len(data) // fft_size // sampleSize

    num_samples   = len(data) // sampleSize

    # ok compute how many data to one row
    num_rows_real = 1024
    if num_rows < num_rows_real:
        num_rows_real = num_rows

    # find DC part
    DC_PART = 0
    for i in range(num_rows):
        subdata_start = i * sampleSize * fft_size
        subdata = data[subdata_start : subdata_start + fft_size * sampleSize]
        
        subdata = subdata[1::2] + 1j * subdata[0::2] # convert to complex
        DC_PART += np.sum(subdata)

    DC_PART /= num_samples

    print(f"DC part is {DC_PART}")

    abstract_rows_per_row = int(num_rows / num_rows_real)
    
    spectrogram = np.zeros((num_rows_real, fft_size))
    
    sub_fft = None
    for i in range(abstract_rows_per_row * num_rows_real):

        subdata_start = i * sampleSize * fft_size
        subdata = data[subdata_start : subdata_start + fft_size * sampleSize]
        
        subdata = subdata[1::2] + 1j * subdata[0::2] # convert to complex
        sub_fft -= DC_PART                           # remove DC part
        
        cur_fft = 10 * np.log10(np.abs(np.fft.fftshift(np.fft.fft(subdata)))**2)
            
        if sub_fft is None:
            sub_fft = cur_fft
        else:
            sub_fft = np.mean(np.array([cur_fft, sub_fft]), axis=0) 
        
        if i % abstract_rows_per_row == 0:
            img_row = int(i // abstract_rows_per_row)
            spectrogram[img_row,:] = sub_fft
            sub_fft          = None
        
    fig = plt.figure(figsize=(5, num_rows / sample_rate * 20))
    plt.imshow(spectrogram, cmap=plt.get_cmap('winter'), aspect='auto', extent = [sample_rate/-2/1e6 + Fc/1e6, sample_rate/2/1e6 + Fc/1e6, 0, len(data)/sample_rate], vmin=0, vmax=np.max(spectrogram))
    plt.xlabel("Frequency [MHz]")
    plt.ylabel("Time [s]")
    plt.savefig(args.output_file) 
