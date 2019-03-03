#include <stdio.h>
#include <stdbool.h>
#include <string.h>

// Solution must be XXXXX-XXXXX-XXXXX-XXXXX-XXXXX
_Bool check_01(char * key)                                                  {
    if(key[5] != '-' || key[11] != '-' || 
       key[17]!= '-' || key[23] != '-')                                     {
        //printf("Failed Check 1.\n");
        return false;                                                       }
    else                                                                    {
        return true;                                                        }
                                                                            }
// Solution must be X#XX#-#XX#X-XXX#X-#XXX#-XXX##
_Bool check_02(char * key)                                                  {
    if((unsigned int)(key[1] - '0') < 10 && 
       (unsigned int)(key[4] - '0') < 10 && 
       (unsigned int)(key[6] - '0') < 10 && 
       (unsigned int)(key[9] - '0') < 10 && 
       (unsigned int)(key[15] - '0') < 10 && 
       (unsigned int)(key[18] - '0') < 10 && 
       (unsigned int)(key[22] - '0') < 10 && 
       (unsigned int)(key[27] - '0') < 10 && 
       (unsigned int)(key[28] - '0') < 10)                                   {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check2.\n");
        return false;                                                       }
                                                                            }

// Solution must be X4XX9-#XX7X-XXX#X-#XXX#-XXX##
_Bool check_03(char * key)                                                  {
    if((key[4] - '0') == (key[1]-'0')*2+1 && 
       (key[4] - '0') > 7    && 
       (key[9] == (key[4])-(key[1]-'0')+2))                             {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check3.\n");
        return false;                                                       }
                                                                            }
// Solution must be X4XX9-#XX7X-XXX#X-#XXX#-XXX88
_Bool check_04(char * key)                                                  {
    if((key[27] + key[28])%13==8)                                           {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check4.\n");
        return false;                                                       }
                                                                            }
// Solution must be X4XX9-#XX7X-XXX#X-#XXX2-XXX88
_Bool check_05(char * key)                                                  {
    if((key[27] + key[22])%22==18)                                          {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check5.\n");
        return false;                                                       }
                                                                            }
// Solution must be X4XX9-#XX7X-XXX#X-6XXX2-XXX88
_Bool check_06(char * key)                                                  {
    if((key[18] + key[22])%11==5)                                           {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check6.\n");
        return false;                                                       }
                                                                            }
// Solution must be X4XX9-#XX7X-XXX#X-6XXX2-XXX88
_Bool check_07(char * key)                                                  {
    if((key[28] + key[22] + key[18])%26==4)                                 {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check7.\n");
        return false;                                                       }
                                                                            }
// Solution must be X4XX9-8XX7X-XXX#X-6XXX2-XXX88
_Bool check_08(char * key)                                                  {
    if((key[1] + key[4]*key[6])%41==5)                                      {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check8.\n");
        return false;                                                       }
                                                                            }
// Solution must be X4XX9-8XX7X-XXX9X-6XXX2-XXX88
_Bool check_09(char * key)                                                  {
    if((key[15] - key[28])%4==1)                                            {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check9.\n");
        return false;                                                       }
                                                                            }
// Solution must be X4XX9-8XX7X-XXX9X-6XXX2-XXX88
_Bool check_0A(char * key)                                                  {
    if((key[4] + key[22])%4==3)                                             {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check10. %d\n", (key[4] + key[22])%4);
        return false;                                                       }
                                                                            }

// Solution must be X4XX9-8XX7X-XXX9X-6XBB2-XXX88
_Bool check_0B(char * key)                                                  {
    if(key[20] == 'B' && key[21] == 'B')                                    {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check11.\n");
        return false;                                                       }
                                                                            }

// Solution must be X4XX9-8XX7X-XXX9X-6XBB2-XXX88
_Bool check_0C(char * key)                                                  {
    if((key[6] + key[15]*key[9])%10==1)                                     {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check12 %d.\n",(key[6] + key[15]*key[9])%10 );
        return false;                                                       }
                                                                            }

// Solution must be X4XX9-8XX7X-XXX9X-6XBB2-XXX88
_Bool check_0D(char * key)                                                  {
    if((key[4] + key[15] + key[27] - 18)%16==8)                             {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check13.\n");
        return false;                                                       }
                                                                            }
// Solution must be X4XX9-8XX7X-XXX9X-6XBB2-XXX88
_Bool check_0E(char * key)                                                  {
    if((key[28] - key[9])%2==1)                                             {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check14.\n");
        return false;                                                       }
                                                                            }
// Solution must be M4XX9-8XX7X-XXX9X-6XBB2-XXX88
_Bool check_0F(char * key)                                                  {
    if(key[0] == 'M')                                                       {
        return true;                                                        }
    else                                                                    {
        //printf("Failed Check15.\n");
        return false;                                                       }
                                                                            }



_Bool verify_key(char * key)                                                {
    if(strlen(key) < 29)                                                    { 
        printf("Key was too short %d.\n", strlen(key));
        return false;                                                       }
    return check_01(key)
        && check_02(key)
        && check_03(key)
        && check_04(key)
        && check_05(key)
        && check_06(key)
        && check_07(key)
        && check_08(key)
        && check_09(key)
        && check_0A(key)
        && check_0B(key)
        && check_0C(key)
        && check_0D(key)
        && check_0E(key)
        && check_0F(key);
                                                                            }

int main()                                                                  {
    setvbuf(stdout,0, _IONBF,0);
    printf("\nPlease Enter a product key to continue: \n");
    char pkey[30];
    fgets((char*)pkey, 30, stdin);
    if (verify_key(pkey))                                                   {
        FILE* infile = fopen("flag.txt", "r");
        if (infile == NULL)                                                 {
            printf("Too bad the flag is only on the remote server!\n");
            return 0;                                                       }
        char output[100];
        fgets(output, 100, infile);
        printf("%s", output);                                                     }
                                                                            }
