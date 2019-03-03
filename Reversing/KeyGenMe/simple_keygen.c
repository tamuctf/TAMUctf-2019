#include <stdio.h>
#include <stdbool.h>
#include <string.h>
#include <stdlib.h>

char * enc(const char * key)
{
    char * result = (char *) malloc(64);
    
    int length = strlen(key);
    unsigned char seed = 0x48;
    for(int i = 0; i < length; i++)
    {
        result[i] = 48+(seed*key[i] + seed*12 + 17)%70;
        seed = result[i];
    }
    return result; 
}

_Bool verify_key(char * key)
{
    if(strlen(key) < 10 || strlen(key) > 64)
    {
        return false;
    }
    char * result = enc(key);
    // random password: jfZxShcfa7hcX9cn
    char * compare = "[OIonU2_<__nK<KsK";
    return !strcmp(compare, result);
}

int main()                                                                  
{
    setvbuf(stdout,0, _IONBF,0);
    printf("\nPlease Enter a product key to continue: \n");
    char pkey[65];
    fgets((char*)pkey, 65, stdin);
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
