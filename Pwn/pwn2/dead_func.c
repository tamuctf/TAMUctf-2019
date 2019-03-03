#include <stdio.h>
#include <stdlib.h>



void two()
{
    printf("This is function two!\n");
}

void print_flag()
{
    printf("This function is still under development.\n");
    FILE* fp = fopen("flag.txt", "r");
    char ch;
    while((ch = getc(fp)) != EOF)
        printf("%c",ch);
    printf("\n");
}

void one()
{
    printf("This is function one!\n");
}

void select_func(char* inp)
{
    void (*func)() = &two;
    char buf[30];
    strncpy(buf, inp, 31);
    if(strcmp(buf,"one") == 0)
        func = &one;
    (*func)();
}

int main()
{
    setvbuf(stdout,_IONBF,0,0);
    printf("Which function would you like to call?\n");
    char inp[31];
    gets(inp);
    select_func(inp);
}
