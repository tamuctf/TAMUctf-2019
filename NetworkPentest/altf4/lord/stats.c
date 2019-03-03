#include <stdio.h>

static const char STATS_PATH[] = "/var/run/stats";

int main() {
    int num;
    FILE *fptr;
    char str[1024];

    if ((fptr = fopen(STATS_PATH,"r")) == NULL){
       printf("error opening %s\n", STATS_PATH);
       return 1;
    }

    if (fgets(str, 1024, fptr) != NULL) {
        puts(str);
    } else {
        printf("no stats\n");
        return 1;
    }
    fclose(fptr);
    return 0;
}
