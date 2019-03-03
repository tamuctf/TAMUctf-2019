#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include "common.h"

#define clear() printf("\033[H\033[J")

typedef struct global_object_t
{
  int server_fd;
  int logged_in;
  int priv;
} global_object_t;

void init_state(global_object_t* state)
{
  memset(state, 0, sizeof(*state));
}

void connect_to_server(global_object_t* state, char* host)
{
  struct sockaddr_in addr;
  struct sockaddr_in server_addr;

  state->server_fd = socket(AF_INET, SOCK_STREAM, 0);
  if(state->server_fd < 0)
  {
    perror("Socket");
    exit(-1);
  }
  memset(&server_addr, 0, sizeof(server_addr));
  server_addr.sin_family = AF_INET;
  server_addr.sin_port = htons(PORT);

  if(inet_pton(AF_INET, host, &server_addr.sin_addr) <= 0)
  {
    perror("Invalid address");
    exit(-1);
  }

  if(connect(state->server_fd, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0)
  {
    perror("Connect");
    exit(-1);
  }
}

void cleanup(global_object_t* state)
{
  if(state->server_fd)
  {
    close(state->server_fd);
  }
}

void send_msg(global_object_t* state, int cid, char* msg, unsigned int size)
{
  int needtosend = size + sizeof(msg_header_t);
  int result;
  char* buf = malloc(size + sizeof(msg_header_t));
  char* start = buf;
  msg_header_t* header = (msg_header_t*)buf;

  header->msg_size = size;
  header->cmd_id = cid;
  memcpy(buf + sizeof(msg_header_t), msg, size);

  while(needtosend > 0)
  {
    result = write(state->server_fd, buf, needtosend);
    if(result < 0)
    {
      perror("Send");
      exit(-1);
    }
    buf += result;
    needtosend -= result;
  }

  free(start);
}

void print_options(global_object_t* state)
{
  int i = 0;
  printf("\t 0. View Recent Login's With client\n");
  printf("\t 1. Login\n");
  if(state->logged_in){
    printf("\t 2. Check Balance Of Account\n");
    printf("\t 3. Create An Account\n");
    printf("\t 4. Deposit Money\n");
    printf("\t 5. Send Money to Another Account\n");
    printf("\t 6. Update Account Information\n");
    if(state->priv > 7)
    {
      printf("\t 7. Interact with On Hold Customers\n");
      ++i;
      if(state->priv > 9){
        printf("\t 8. Send Money Between Accounts\n");
        printf("\t 9. Create A Login User\n");
        i+=2;
      } 
    }
    printf("\t 10. Contact Support\n");
    printf("\t 11. Check Connection to Bank Portal\n");
    printf("\t 12. Exit Bank Portal\n");
  }
  printf("Enter command to send to server...\n");
}

void prompt_int(int * input)
{
  scanf("%d", input);
}

void prompt_string(char string[256], int length)
{
  fgets(string,length,stdin);
}

int send_login(global_object_t* state)
{
  // initializing username and password field
  printf("Input Username for login:\n");
  char username[512];
  prompt_string(username, 256);
  printf("Input Password for login:\n");
  char password[256];
  prompt_string(password, 256);
  
  char * msg_body = malloc(514);
  
  // setting up message with username+password
  unsigned char user_len = strlen(username)-1;
  unsigned char pass_len = strlen(password)-1;
  username[user_len] = 0;
  password[pass_len] = 0;
  
  memcpy(msg_body, &user_len, 1);
  memcpy(msg_body + 1, &pass_len, 1);
  memcpy(msg_body + 2, username, user_len);
  memcpy(msg_body + 2 + user_len, password, pass_len);
  
  // sending datagram to server
  send_msg(state, 0, msg_body, user_len + pass_len + 2);
  printf("Message sent to server.\n");
  read(state->server_fd, &(state->priv), sizeof(int));
  sleep(2);
  if(state->priv < 0)
  {
    return -2;
  }
  state->logged_in = 1;
  return 1;
}

int send_check_balance(global_object_t* state)
{
  int choice;
  // initializing username and password field
  printf("Enter Account to Check Balance of:\n");
  scanf("%d", &choice);
  getchar();
  
  char * msg_body = malloc(4);

  memcpy(msg_body, &choice, 4);
  
  // sending datagram to server
  send_msg(state, 1, msg_body, 4 + 2);
  printf("Message sent to server.\n");
  double balance;
  read(state->server_fd, &balance, sizeof(double));
  printf("You have a balance of %f\n", balance);
  sleep(2);
  return 1;
}

int send_create_account(global_object_t* state)
{
  unsigned int choice;
  // initializing username and password field
  printf("Enter Account Number for Your New Account:\n");
  scanf("%d", &choice);
  getchar();
  
  char * msg_body = malloc(4);
 
  memcpy(msg_body, &choice, 4);
  
  // sending datagram to server
  send_msg(state, 2, msg_body, 4 + 2);
  printf("Message sent to server.\n");
  int result;
  read(state->server_fd, &(result), sizeof(int));
  sleep(2);
  return result;
}

int send_deposit(global_object_t* state)
{
  char * msg_body = malloc(3*sizeof(unsigned int) + sizeof(double) + 8*sizeof(char));
  unsigned int account_no;
  unsigned int check_no;
  double value;
  char * date = "3/14/18";
  unsigned int date_len = strlen(date);
  
  printf("What is the account number to which you would like to deposit?\n");
  scanf("%d", &account_no);
  getchar();
  printf("What is the check number listed at the top of your check? (ive seen it both ways)\n");
  scanf("%d", &check_no);
  getchar();
  printf("How much is your check for?\n");
  scanf("%lf", &value);
  getchar();
  
  memcpy(msg_body, &date_len, 4);
  memcpy(msg_body+4, &account_no, 4);
  memcpy(msg_body+8, &check_no, 4);
  memcpy(msg_body+12, &value, sizeof(double));
  memcpy(msg_body+12+sizeof(double), date, date_len);
  
  send_msg(state, 3, msg_body, 3*sizeof(unsigned int) + sizeof(double) + 8*sizeof(char));
  printf("Message sent to server.\n");
  int result;
  read(state->server_fd, &(result), sizeof(int));
  sleep(2);
  return result;
}

int send_transfer(global_object_t* state)
{
  char username[512];
  printf("What is the Username of the person you would like to send money to?\n");
  prompt_string(username, 256);
  int to_len = strlen(username);
  
  char * date = "3/14/18";
  unsigned int date_len = strlen(date);
  
  printf("What is their account number to which you would like to deposit?\n");
  unsigned int to_account;
  scanf("%d", &to_account);
  getchar();
  
  printf("What is your account number you want to pull from?\n");
  unsigned int fro_account;
  scanf("%d", &fro_account);
  getchar();
  
  printf("What is the check number listed at the top of your check? (ive seen it both ways)\n");
  unsigned int check_no;
  scanf("%d", &check_no);
  getchar();
  
  printf("How much do you want to send?\n");
  double value;
  scanf("%lf", &value);
  getchar(); 
  
  char * msg_body = malloc(4*sizeof(unsigned int) + sizeof(double) + 8*sizeof(char) + to_len);
  
  memcpy(msg_body, &to_len, 1);
  memcpy(msg_body+1, &date_len, 1);
  memcpy(msg_body+4, &check_no, 4);
  memcpy(msg_body+8, &fro_account, 4);
  memcpy(msg_body+12, &to_account, 4);
  memcpy(msg_body+16, &value, sizeof(double));
  memcpy(msg_body+16+sizeof(double), username, to_len);
  memcpy(msg_body+16+sizeof(double)+to_len, date, date_len);
  
  send_msg(state, 6, msg_body, 4*sizeof(unsigned int) + sizeof(double) + 8*sizeof(char) + to_len);
  printf("Message sent to server.\n");
  int result;
  read(state->server_fd, &(result), sizeof(int));
  sleep(2);
  return result;
}

int send_update(global_object_t* state)
{
  unsigned int choice;
  char field[256];
  printf("Which Field would you like updated...\n");
  printf("\tEnter (1) to modify Email Address...\n");
  printf("\tEnter (2) to modify Name on Account...\n");
  printf("\tEnter (3) to change Security Question's Answer...\n");
  scanf("%d", &choice);
  getchar();
  printf("What would you like to change the field to?\n");
  prompt_string(field, 256);
  
  char * msg_body = malloc(strlen(field) + 4);
  
  unsigned char length = strlen(field) - 1;
  
  memcpy(msg_body, &choice, 1);
  memcpy(msg_body+1, &length, 1);
  memcpy(msg_body+2, field, length);
  
  send_msg(state, 9, msg_body, length + 2);
  printf("Message sent to server.\n");
  int result;
  read(state->server_fd, &(result), sizeof(int));
  sleep(2);
  return result;
}

int send_keep_alive(global_object_t* state)
{
  char * field = "2qzt9QjaGDZp\?({";
  char * msg_body = malloc(16 + 4);
  
  unsigned char length = 16;
  
  memcpy(msg_body, &length, 1);
  memcpy(msg_body+1, field, length);
  
  send_msg(state, 8, msg_body, length + 2);
  printf("Message sent to server.\n");
  int result;
  read(state->server_fd, &(result), sizeof(int));
  sleep(2);
  return result;
}

int send_play_game(global_object_t* state)
{
  printf("Pairing with Customer who is currently on hold, loading activity...\n");
  sleep(3);
  printf("\tCurrently Loaded Activity is Rock, Paper, Scissors\n");
  printf("\tTo throw ((Rock)), enter 1.\n");
  printf("\tTo throw ((Paper)), enter 2.\n");
  printf("\tTo throw ((Scissors)), enter 3.\n");
  unsigned int choice;
  scanf("%d", &choice);
  getchar();
  send_msg(state, 74, "water", 5);
  printf("Message sent to server.\n");
  unsigned char size;
  char* result;
  read(state->server_fd, &(size), sizeof(1));
  printf("size of message is %i", size);
  read(state->server_fd, &(result), 15);
  printf(result);
  sleep(2);
  return 1;
}

int send_arb_transfer(global_object_t* state)
{
  char toward[512];
  printf("What is the Username of the person you would like to send money to?\n");
  prompt_string(toward, 256);
  int to_len = strlen(toward);
  
  char from[512];
  printf("What is the Username of the person you would like to send money from?\n");
  prompt_string(from, 256);
  int fro_len = strlen(from);
  
  char * date = "3/14/18";
  unsigned int date_len = strlen(date);
  
  printf("What is their account number of the destination user?\n");
  unsigned int to_account;
  scanf("%d", &to_account);
  getchar();
  
  printf("What is their account number of the source user?\n");
  unsigned int fro_account;
  scanf("%d", &fro_account);
  getchar();
  
  printf("What is the check number listed at the top of your check? (ive seen it both ways)\n");
  unsigned int check_no;
  scanf("%d", &check_no);
  getchar();
  
  printf("How much do you want to send?\n");
  double value;
  scanf("%lf", &value);
  getchar(); 
  
  char * msg_body = malloc(4*sizeof(unsigned int) + sizeof(double) + 8*sizeof(char) + to_len + fro_len);
  
  memcpy(msg_body, &to_len, 1);
  memcpy(msg_body+1, &fro_len, 1);
  memcpy(msg_body+2, &date_len, 1);
  memcpy(msg_body+4, &check_no, 4);
  memcpy(msg_body+8, &fro_account, 4);
  memcpy(msg_body+12, &to_account, 4);
  memcpy(msg_body+16, &value, sizeof(double));
  memcpy(msg_body+16+sizeof(double), toward, to_len);
  memcpy(msg_body+16+sizeof(double)+to_len, from, fro_len);
  memcpy(msg_body+16+sizeof(double)+to_len+fro_len, date, date_len);
  
  send_msg(state, 96, msg_body, 4*sizeof(unsigned int) + sizeof(double) + 8*sizeof(char) + to_len+ fro_len);
  printf("Message sent to server.\n");
  int result;
  read(state->server_fd, &(result), sizeof(int));
  sleep(2);
  return result;
}

int send_create_user(global_object_t* state)
{
  char username[512];
  printf("What is the username of the new account?\n");
  prompt_string(username, 256);
  int user_len = strlen(username)-1;
  
  char password[512];
  printf("What is the password of the new account?\n");
  prompt_string(password, 256);
  int pass_len = strlen(password)-1;
  
  printf("What priveledge level is this user (1<upper management>-10<admin>)\n");
  unsigned char priv;
  scanf("%d", &priv);
  getchar();
  
  char email[512];
  printf("What is email?\n");
  prompt_string(email, 256);
  int email_len = strlen(email)-1;
  
  char name[512];
  printf("What is the Name of user?\n");
  prompt_string(name, 256);
  int name_len = strlen(name)-1;
  
  printf("For security purposes, there must be a challenge question and answer\n");
  printf("\tTo select ((Favorite Color)),          enter 1.\n");
  printf("\tTo select ((Name of favorite pet)), enter 2.\n");
  printf("\tTo select ((Favorite TV show)), enter 3.\n");
  printf("\tTo select ((Favorite Movie)), enter 4.\n");
  unsigned char choice;
  scanf("%d", &choice);
  getchar();
  
  char answer[512];
  printf("What is the answer to the security question?\n");
  prompt_string(answer, 256);
  int answer_len = strlen(answer)-1;
  
  char * msg_body = malloc(7+user_len+pass_len+email_len+name_len+answer_len);
  
  memcpy(msg_body, &user_len, 1);
  memcpy(msg_body+1, &pass_len, 1);
  memcpy(msg_body+2, &priv, 1);
  memcpy(msg_body+3, &email_len, 1);
  memcpy(msg_body+4, &name_len, 1);
  memcpy(msg_body+5, &answer_len, 1);
  memcpy(msg_body+6, &choice, 1);
  memcpy(msg_body+7, username, user_len);
  memcpy(msg_body+7+user_len, password, pass_len);
  memcpy(msg_body+7+user_len+pass_len, email, email_len);
  memcpy(msg_body+7+user_len+pass_len+email_len, name, name_len);
  memcpy(msg_body+7+user_len+pass_len+email_len+name_len, answer, answer_len);
    
  send_msg(state, 97, msg_body, 7+user_len+pass_len+email_len+name_len+answer_len);
  printf("Message sent to server.\n");
  int result;
  read(state->server_fd, &(result), sizeof(int));
  sleep(2);
  return result;
}

int send_pointless_crash(global_object_t* state)
{
  send_msg(state, 98, "*2767*3855#", 11);
  return 1;
}

print_remembered(){
  printf("Most recent login's from this client\n");
  printf("1. 1337\n");
  printf("2. 23646\n");
  printf("...\n");
  sleep(4);
}

void user_menu(global_object_t* state)
{
  unsigned int choice = 255;
  unsigned char bad_attempts = 0;
  int reply = 0;

  do
  {
    clear();
    print_options(state);
    scanf("%d", &choice);
    getchar();
    switch (choice)
    {
      case 0:
        /* Remember Me Request*/
        reply = 1;
        print_remembered();
        break;
      case 1:
        /* Login Request*/
        reply = send_login(state);
        break;
      default:
        if(state->logged_in)
        {  
          switch(choice)
          {          
            case 2:
              /*Check Balance*/
              reply = send_check_balance(state);
              break;
            case 3:
              /*Create An Account*/          
              reply = send_create_account(state);
              break;
            case 4:
              /*Deposit Money*/          
              reply = send_deposit(state);
              break;
            case 5:
              /*Send Money to Another Account*/          
              reply = send_transfer(state);
              break;
            case 6:
              /*Update Account Information*/          
              reply = send_update(state);
              break;
            case 7:
              /*Interact with On Hold Customers*/          
              reply = send_play_game(state);
              break;
            case 8:
              /*Send Money Between Accounts*/          
              reply = send_arb_transfer(state);
              break;
            case 9:
              /*Create a New User*/          
              reply = send_create_user(state);
              break;
            case 10:
              /*Dereference Null*/          
              reply = send_pointless_crash(state);
              break;
            case 11:
              /*Send Heart Beat*/          
              reply = send_keep_alive(state);
              break;
            case 12:
              /*Graceful exit*/          
              goto exit;
              break;         
            default:
              printf("Wrong choice.Enter Again\n");
              bad_attempts+=1;
              if(bad_attempts == 3)
              {  
                printf("Too many bad inputs...\n");
                return;
              }
              break;
          }
        } else
        {
          printf("Wrong choice.Enter Again\n");
          bad_attempts+=1;
          if(bad_attempts == 3)
          {  
            printf("Too many bad inputs...\n");
            return;
          }
          continue;
        }  
    }
    if(reply == -1)
    {
      printf("Server did not like request\n");
      return;
    }
    else if(reply == -2)
    {
      printf("Invalid Credentials. Please try again.\n");
      sleep(3);
    }
    bad_attempts = 0;
  } while(choice != 12);
  exit:
  printf("Thank you\n");
}

int main(int argc, char** argv)
{
  global_object_t state;
  init_state(&state);
  const char * ip;
  // Confirm that this is working?
  if(argc < 2)
  {
    printf("Usage: client <server ip address>\n");
    return 0;
  }
  else
    ip = argv[1];
  connect_to_server(&state, ip);
  user_menu(&state);
  cleanup(&state);

  return 0;
}
