#include "unistd.h"
#include "stdio.h"
#include "stdlib.h"
#include "string.h"

void run_cmd(char* user_in) {
    char user_cmd[10];
    snprintf(user_cmd, 7, "ls %s", user_in);
    printf("Result of %s:\n", user_cmd);
    system(user_cmd);
}

void laas() {
    printf("ls as a service (laas)(Copyright pending)\n");
    printf("Version 2: Less secret strings and more portable!\n");
    printf("Enter the arguments you would like to pass to ls:\n");
    char user_in[5];
    gets(user_in);
    if(strchr(user_in, '/') == NULL)
        run_cmd(user_in);
    else
        printf("No slashes allowed\n");
}

int main() {
    setvbuf(stdout,_IONBF,0,0);
    while(1)
        laas();
}
