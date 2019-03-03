
# Hello World

## Challenge

Objective is for the player to recognize that the extra whitespace above the Hello World C++ program is from the Whitespace language, and to extract the flag from that.

## Solution

A program to print a string is embedded in the C++ program as Whitespace. If you run an exact copy of the C++ program, whitespace and all, using a whitespace compiler such as https://vii5ard.github.io/whitespace/ then it would print out a sentence. I wrote the Whitespace program so that it would not print out all of the text that I pushed to the stack, so you need to convert the Whitespace to the corresponding stack push commands, either manually by learning Whitespace via https://web.archive.org/web/20150424165140/http://compsoc.dur.ac.uk/whitespace/index.php or looking at the 'debug' diaply on the compiler I linked. You can see integer values being pushed to the stack. If you gather all of the values and convert them as ASCII values to characters, you will get the flag gigem{0h_my_wh4t_sp4c1ng_y0u_h4v3}. A path to success would be...

1) Identify that the C++ program has Whitespace embedded in it.
2) Find a compiler and run it, getting the string "Well sweet golly gee, that sure is a lot of whitespace!".
3) Identify that there are more push commands to the stack than there are print commands, meaning that not all the text is printed.
4) Convert the Whitespace to their push commands to get the ASCII values for each push, which will reveal the flag as what's not being printed. 
