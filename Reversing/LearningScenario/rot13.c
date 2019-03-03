#include <stdio.h>
#include <string.h>
#include <math.h>

int ra(int c){
  if('a' <= c && c <= 'z'){
    return rb(c,'a');
  } else if ('A' <= c && c <= 'Z') {
    return rb(c, 'A');
  } else {
    return c;
  }
}

int rb(int c, int basis){
  c = (((c-basis)+13)%26)+basis;
  return c;
}

void app(char* s, char c) {
        int len = strlen(s);
        s[len] = c;
        s[len+1] = '\0';
}

int main() {
  
	char alpha[26] = {'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','x','y','z'};
	char x=0, y, flag, em, gigme[5], JIk[5];
	unsigned char ans, answ, gig;
	int i=0, lG;
	while(i<3) {
		gig = 150;
		em = 25;
		y = 13;
		flag = 2;
		ans = (((gig + em)/y)*(flag*flag*flag))+(x*x*x);
		answ = round(ans);
		x += 1;
		
		app(gigme, answ);
		i += 1;
	}
	lG = sizeof(gigme);

	for (i = 0; i <= lG; i++)
		JIk[i] = ra(gigme[i]);


  return 0;
}