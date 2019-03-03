#include <stdio.h>
#include <string.h>
#include <stdlib.h>


char* concat(const char *s1, const char *s2)
{
	char *result = malloc(strlen(s1) + strlen(s2) + 1);
	strcpy(result, s1);
	strcpy(result, s2);
	return result;
}


int main() 
{
	/*Comments*/

	//char ans[] = {65, 53, 53, 51, 77, 98, 49, 89};
	char ans[8] = "";
	ans[0]=65;
	ans[1]=53;
	ans[2]=53;
	ans[3]=51;
	ans[4]=77;
	ans[5]=98;
	ans[6]=49;
	ans[7]=89;



	int i;
	
	int x=0, y=1, z=2, a, b;
	a = z*z*z*(x+y+y+y)/14;
	b = z*z*z*(x+y+y)/3;

	printf("The answer: %d\n", a);
	printf("Maybe it's this:%d\n", b);

	printf("gigem{%s}\n",ans);


	return 0;
}