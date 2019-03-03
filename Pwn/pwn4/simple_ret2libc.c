#include "unistd.h"
#include "stdio.h"
#include "stdlib.h"

char secret[] = "/bin/sh";

void run_cmd(char* user_in) {
    char user_cmd[30];
    snprintf(user_cmd, 27, "ls %s", user_in);
    printf("Result of %s:\n", user_cmd);
    system(user_cmd);
}

void laas() {
    printf("ls as a service (laas)(Copyright pending)\n");
    printf("Enter the arguments you would like to pass to ls:\n");
    char user_in[25];
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
