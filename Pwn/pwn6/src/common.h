#ifndef COMMON_H
#define COMMON_H
#include <sqlite3.h>

#define PORT 6210

typedef struct msg_header_t
{
  uint32_t msg_size;
  uint32_t cmd_id;
} msg_header_t;


typedef struct login_msg_t
{
  unsigned char user_len;
  unsigned char pass_len;
  char* username;
  char* password;
} login_msg_t;

typedef struct echo_msg_t
{
  unsigned char keep_alive_len;
  char * keep_alive;
} echo_msg_t;

typedef struct deposit_msg_t
{
  unsigned int date_len;
  unsigned int account_no;
  unsigned int check_no;
  double value;
  char * date;
} deposit_msg_t;

typedef struct faulty_deposit_msg_t
{
  unsigned int date_len;
  unsigned int account_no;
  unsigned int check_no;
  double value;
  char date[64];
} faulty_deposit_msg_t;

typedef struct balance_msg_t
{
  unsigned int account_no;
} balance_msg_t;

typedef struct new_login_msg_t
{
  unsigned char user_len;
  unsigned char pass_len;
  unsigned char priv;
  unsigned char email_len;
  unsigned char name_len;
  unsigned char security_len;
  unsigned char security_quest;
  char* username;
  char* password;
  char* email;
  char* name;
  char* security_answer;
} new_login_msg_t;

typedef struct new_account_msg_t
{
  unsigned int account_no;
} new_account_msg_t;

typedef struct update_msg_t
{
  unsigned char swtch;
  unsigned char field_len;
  char* field;
} update_msg_t;

typedef struct transfer_msg_t
{
  unsigned char to_len;
  unsigned char date_len;
  unsigned int check_no;
  unsigned int fro_acc;
  unsigned int to_acc;
  double amount;
  char* toward;
  char* date;
} transfer_msg_t;

typedef struct arb_transfer_msg_t
{
  unsigned char to_len;
  unsigned char fro_len;
  unsigned char date_len;
  unsigned int check_no;
  unsigned int to_acc;
  unsigned int fro_acc;
  double amount;
  char* toward;
  char* from;
  char* date;
} arb_transfer_msg_t;

typedef struct fib_msg_t
{
  int n;
} fib_msg_t;

typedef struct ackermann_function_msg_t
{
  int m_bits;
  int n_bits;
  int m;
  int n;
} ackermann_function_msg_t;

#endif
