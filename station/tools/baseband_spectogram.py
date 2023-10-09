#!/usr/bin/env python
# Simple spectogram ploter

import argparse
import numpy as np
import matplotlib.pyplot as plt

if __name__ == '__main__':
    cliParser = argparse.ArgumentParser(description='Simple spectogram ploter')    
    
    cliParser.add_argument('input_file',  type=str, help='input filename')
    cliParser.add_argument('output_file', type=str, help='output filename')
    cliParser.add_argument('-fs', '--sampleRate',  type=float, help='sets the sample rate [hz]')    
    cliParser.add_argument('-fc', '--centralFreq', type=float, help='sets the sample rate [hz]')    
    
    cliParser.add_argument('-f', '--format', type=str, 
        help='Output format', 
        choices=["int8"],
        default='int8')

    args = cliParser.parse_args()
    
    data = np.fromfile(args.input_file, dtype=args.format)
    
    data = data[1::2] + 1j * data[0::2]
    
    fft_size    = 1024
    sample_rate = args.sampleRate
    Fc          = args.centralFreq
    num_rows    = len(data) // fft_size # // is an integer division which rounds down
    
    spectrogram = np.zeros((num_rows, fft_size))
    
    for i in range(num_rows):
        spectrogram[i,:] = 10 * np.log10(np.abs(np.fft.fftshift(np.fft.fft(data[i*fft_size:(i+1)*fft_size])))**2)
        
    fig = plt.figure(figsize=(5, num_rows / sample_rate * 20))
    
    plt.imshow(spectrogram, cmap=plt.get_cmap('winter'), aspect='auto', extent = [sample_rate/-2/1e6 + Fc/1e6, sample_rate/2/1e6 + Fc/1e6, 0, len(data)/sample_rate], vmin=0, vmax=np.max(spectrogram))
    plt.xlabel("Frequency [MHz]")
    plt.ylabel("Time [s]")
    plt.savefig(args.output_file) 