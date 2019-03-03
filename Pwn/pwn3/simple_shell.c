#include <stdio.h>
#include <stdlib.h>

void echo()
{
    char buf[290];
    printf("Take this, you might need it on your journey %p!\n", buf);
    gets(buf);
}

int main()
{
    setvbuf(stdout,_IONBF,0,0);
    echo();
}
