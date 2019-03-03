import sys
import re

input_morse = open('morse_verbalized.txt', 'r')
morse_verbalized = input_morse.read()
print morse_verbalized

output_morse = open('morse_deverbalized.txt', 'w+')

size = len(morse_verbalized)
print size

top_level_array = re.findall(r"[\w'-]+", morse_verbalized)
for index, each in enumerate(top_level_array):
    top_level_array[index] = each.split('-')
print top_level_array

for index, each in enumerate(top_level_array):
    for index2, each2 in enumerate(each):
        if each2 == 'dah':
            output_morse.write('-')
            if(index2 == len(each) - 1):
                output_morse.write(" ")
        elif each2 == 'dit':
            output_morse.write('.')
            output_morse.write(" ")
        else:
            output_morse.write('.')
