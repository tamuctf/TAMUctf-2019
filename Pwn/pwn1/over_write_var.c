#include <stdio.h>
#include <stdlib.h>

void print_flag()
{
    printf("Right. Off you go.\n");
    FILE* fp = fopen("flag.txt", "r");
    
    char ch;
    while((ch = getc(fp)) != EOF)
        printf("%c",ch);
    printf("\n");
}

int main()
{
    setvbuf(stdout,_IONBF,0,0);
    int junk = 2;
    int secret = 0;
    char answer[43];
    printf("Stop! Who would cross the Bridge of Death must answer me these questions three, ere the other side he see.\n");
    printf("What... is your name?\n");
    fgets(answer, 43, stdin);
    if( strcmp(answer,"Sir Lancelot of Camelot\n") != 0) {
        printf("I don't know that! Auuuuuuuugh!\n");
        exit(0);
    }

    printf("What... is your quest?\n");
    fgets(answer, 43, stdin);
    if( strcmp(answer,"To seek the Holy Grail.\n") != 0) {
        printf("I don't know that! Auuuuuuuugh!\n");
        exit(0);
    }

    printf("What... is my secret?\n");
    gets(answer);
    if(secret == 0xdea110c8)
        print_flag();
    else
        printf("I don't know that! Auuuuuuuugh!\n");
}
