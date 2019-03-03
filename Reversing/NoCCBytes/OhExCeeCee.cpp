//this file should never make it to master, just used for my own testing.
#include <stdio.h>
#include <iostream>
#include <fstream>
#include <string.h>

bool checkFlag = 0;
unsigned char globPass[25] = {'\x46','\x70','\x65','\x65','\x7e','\x42', '\x70','\x68','\x62','\x1b','\x00'};
bool passCheck(char* input);

unsigned char* check(unsigned char* func){
    //the check() function checks itself for the presence of breakpoints as well :)
    if(func != (unsigned char*) check){
        if (check((unsigned char*)check) != (unsigned char*)check){
            return NULL;
        }
    }
    unsigned char val = '\x99';
    unsigned char xorVal = '\x11';
    int i,j;
    unsigned char *p = func;
    //prints the first 64 bytes of the function
    for (j=0; j<4; j++) {
        //printf("\n%p: ",p);
        //iterate through lines of 16
        for (i=0; i<16; i++){
            //this does nothing except help obfuscate how the key is hidden.
            xorVal += i * j;
            *p++;
            //printf("%.2x ", *p);
            //printf("%.2x ","\xcc");
            if ((*p ^ '\x55') == val){
                //printf("Found a breakpoint\n");
                return NULL;
            }
            xorVal -= i*j;
        }
    }
    //if we get here, we can decrypt the global password since there are no breakpoints.
    for (int i = 0; i < sizeof(globPass); i++){
        if(func == (unsigned char*) passCheck){
            if (globPass[i] != '\x00'){
                globPass[i] = globPass[i] ^ xorVal;
            }
            else{
                //do nothing
            }
        }
    }
    //Only gets here if no breakpoints
    checkFlag =  1;
    return func;
}
bool passCheck(char* input){
    if (check((unsigned char*)passCheck) == (unsigned char*)passCheck){\
        //password = WattoSays\n
        unsigned char password[25] = {};
        //copy the globalpass into our password
        strcpy((char*)password, (char*)globPass);
        if(strcmp((char*)input, (char*)password) == 0){
            return true;
        }
        else{
            printf("\nWrong Password\n");

            return false;
        }
    }
    return false;
}

int main(int argc, char const *argv[]) {

    setvbuf(stdout,0, _IONBF,0);
    if(check((unsigned char*)main) == (unsigned char*)main){
        printf("\nWelcome. Please Enter a password to continue: \n");
        char password[25];
        fgets((char*)password, 25, stdin);
        if (passCheck(password)){
            FILE* infile = fopen("flag.txt", "r");
            if (infile == NULL){
                printf("Too bad the flag is only on the remote server!\n");
                return 0;
            }
            char output[100];
            fgets(output, 100, infile);
            printf(output);
        }
    }
    else{

    }
    return 0;
}
