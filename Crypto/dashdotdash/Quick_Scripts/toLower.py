import sys

input_text = open('toLower.txt', 'r')
text = input_text.read()

output_text = open('lowered.txt', 'w+')
output_text.write(text.lower())
