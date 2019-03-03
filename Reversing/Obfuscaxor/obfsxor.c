#include <stdio.h>
#include <stdbool.h>
#include <string.h>
#include <stdlib.h>
#include "instr.h"

char * enc(const char * key)
{
    OBF_BEGIN
    char * result = (char *) malloc(64);
    
    const int length = strlen(key);
    char * seed = (char *) malloc(5);
    seed[0] = N(0xde);
    seed[1] = N(0xad);
    seed[2] = N(0xbe);
    seed[3] = N(0xef);
    seed[4] = N(0x00);
    int i;
    
    FOR(V(i) = N(0), V(i) < length-1, V(i)+=1)
        V(result[i]) = V(key[i])^V(seed[i%4]);
    ENDFOR
    RETURN(result);
    OBF_END 
}

_Bool verify_key(char * key)
{
    if(strlen(key) < 10 || strlen(key) > 64)
    {
        return false;
    }
    char * result = enc(key);
    // random password: p3Asujmn9CEeCB3A
    char * compare = "\xae\x9e\xff\x9c\xab\xc7\xd3\x81\xe7\xee\xfb\x8a\x9d\xef\x8d\xae";
    return !strcmp(compare, result);
}

int main()                                                                  
{
    setvbuf(stdout,0, _IONBF,0);
    printf("\nPlease Enter a product key to continue: \n");
    char pkey[20];
    fgets((char*)pkey, 20, stdin);
    if (verify_key(pkey))                                                   
    {
        FILE* infile = fopen("flag.txt", "r");
        if (infile == NULL)                                                 
        {
            printf("Too bad the flag is only on the remote server!\n");
            return 0;                                                       
        }
        char output[100];
        fgets(output, 100, infile);
        printf("%s", output);                                                     
    }
}
