#include "banking_handlers.h"
#include <crypt.h>
#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>
#include <string.h>
#include <sys/socket.h>
#include <unistd.h>
#include "common.h"

void populate_client_actions(global_object_t* state, client_t* client, int priv)
{
  for(int i = (priv*10)-1; i > 0; --i){
    if(state->actions[i]!=NULL)
    {
      client->client_actions[i] = state->actions[i];
    }
  }
  
  if(priv >= 4 || priv < 3)
  {
    client->client_actions[3] = deposit;
  }
}

int login(global_object_t* state, client_t* client)
{
  sqlite3_stmt* stmt;
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  login_msg_t msg;
  char* pass_nulled;
  int result = -1;

  // first byte is length of userID, second is length of password
  memcpy(&msg, client->recv_buffer + sizeof(msg_header_t), 2);

  //header+2 pointer into userID
  msg.username = client->recv_buffer + sizeof(msg_header_t) + 2;

  //header+2+firstbyte pointer into Password
  msg.password = client->recv_buffer + sizeof(msg_header_t) + 2 + msg.user_len;

  for (int i = 0; i < msg.pass_len ; ++i){
    if(msg.password[i]<65)
      continue;
    else
      msg.password[i] |= ' ';
  }

  printf("Looking up User");
  fflush(stdout); // Will now print everything in the stdout buffer
  for(int i = 0; i < 3; i++)
  {
    //sleep(2);
    printf(".");
    fflush(stdout);
  }
  printf("\n");

  int rc = sqlite3_prepare_v2((state->db), "SELECT * from user where userID == (?1)", -1, &stmt, 0);
  // add userID into string
  sqlite3_bind_text(stmt, 1, msg.username, msg.user_len, SQLITE_STATIC);

  if (rc != SQLITE_OK)
  {
      fprintf(stderr, "Failed to fetch data: %s\n", sqlite3_errmsg((state->db)));
      goto fail;
  }
  rc = sqlite3_step(stmt);
  if (rc != SQLITE_ROW)
  {
    printf("ERROR Selecting: %s\n", sqlite3_errmsg(state->db));
    goto fail;
  }

  pass_nulled = malloc(msg.pass_len + 1);
  memcpy(pass_nulled, msg.password, msg.pass_len);
  pass_nulled[msg.pass_len] = 0;
  char* sha512 = crypt(pass_nulled, "$6$");
  const char* db512 = sqlite3_column_text(stmt, 1);

  if(!strcmp(sha512, db512))
  {
    client->userID = malloc(msg.user_len);
    client->user_len = msg.user_len;
    memcpy(client->userID, msg.username, msg.user_len);
    populate_client_actions(state, client, sqlite3_column_int(stmt, 2));
    result = sqlite3_column_int(stmt, 2);
    send(client->socket, &result, sizeof(result), 0);
    return 1;
  }
  printf("Unauthorized Login Attempt.\n");
fail:
  send(client->socket, &result, 4, 0);
  return -1;
}

int check_balance(global_object_t* state, client_t* client)
{
  sqlite3_stmt* stmt;
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  balance_msg_t msg;
  memcpy(&msg, client->recv_buffer + sizeof(msg_header_t), 4);

  printf("Accessing User Account");
  fflush(stdout); // Will now print everything in the stdout buffer
  for(int i = 0; i < 3; i++)
  {
    //sleep(2);
    printf(".");
    fflush(stdout);
  }
  printf("\n");

  int rc = sqlite3_prepare_v2((state->db), "SELECT * from account where userID == (?1) AND account_no == (?2)", -1, &stmt, 0);
  // add userID into string
  sqlite3_bind_text(stmt, 1, client->userID, client->user_len, SQLITE_STATIC);
  sqlite3_bind_int(stmt, 2, msg.account_no);

  if (rc != SQLITE_OK)
  {
      fprintf(stderr, "Failed to fetch data: %s\n", sqlite3_errmsg((state->db)));
      return -1;
  }
  rc = sqlite3_step(stmt);
  if (rc != SQLITE_ROW)
  {
    printf("ERROR Selecting: %s\n", sqlite3_errmsg(state->db));
    return -1;
  }
  
  double balance = sqlite3_column_double(stmt, 2);
  printf("The client's balance is %f\n", balance);
  send(client->socket, &balance, sizeof(balance), 0);
  return 1;
}

int deposit(global_object_t* state, client_t* client)
{
  sqlite3_stmt* stmt;
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  deposit_msg_t msg;
  unsigned char result = 1;

  // first byte is length of userID, second is length of check number
  memcpy(&msg, client->recv_buffer + sizeof(msg_header_t), 4);

  msg.account_no = *((unsigned int*) (client->recv_buffer + sizeof(msg_header_t) + 4));

  msg.check_no = *((unsigned int*) (client->recv_buffer + sizeof(msg_header_t) + 4 + 4));

  msg.value = *((double*) (client->recv_buffer + sizeof(msg_header_t) + 4 + 8));

  msg.date = malloc(msg.date_len); 
  memcpy(msg.date, client->recv_buffer + sizeof(msg_header_t) + 4 + 16, msg.date_len);

  printf("Validating Check");
  fflush(stdout); // Will now print everything in the stdout buffer
  for(int i = 0; i < 3; i++)
  {
    //sleep(2);
    printf(".");
    fflush(stdout);
  }
  printf("\n");

  int rc = sqlite3_prepare_v2((state->db), "INSERT INTO pend (userID, fro_acc, to_acc, fro, check_no, value, date) VALUES (?1, 0, ?2, ?3, ?4, ?5, ?6) ", -1, &stmt, 0);
  // add userID into string
  sqlite3_bind_text(stmt, 1, client->userID, client->user_len, SQLITE_STATIC);
  sqlite3_bind_int(stmt, 2, msg.account_no);
  sqlite3_bind_text(stmt, 3, "EXTERN0", 7, SQLITE_STATIC);
  sqlite3_bind_int(stmt, 4, msg.check_no);
  sqlite3_bind_double(stmt, 5, msg.value);
  sqlite3_bind_text(stmt, 6, msg.date, 8, SQLITE_STATIC);


  if (rc != SQLITE_OK)
  {
    fprintf(stderr, "Failed to fetch data: %s\n", sqlite3_errmsg((state->db)));
    result = 0;
    send(client->socket, &result, sizeof(result), 0);
    free(msg.date);
    return -1;
  }
  /*
  TODO, check if value is positive
  TODO, check if user exists in DB
  */
  rc = sqlite3_step(stmt);
  send(client->socket, &result, sizeof(result), 0);
  free(msg.date);
  return 1;
}

int keep_alive(global_object_t* state, client_t* client)
{
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  echo_msg_t * msg = ((echo_msg_t*) malloc(400));
  unsigned char result = 1; 
  memcpy(msg, client->recv_buffer + sizeof(msg_header_t), 2);

  printf("Checking if Server is still active");
  fflush(stdout); // Will now print everything in the stdout buffer
  for(int i = 0; i < 3; i++)
  {
    //sleep(2);
    printf(".");
    fflush(stdout);
  }
  printf("\n");

  unsigned int echo_len;
  msg->keep_alive = client->recv_buffer + sizeof(msg_header_t) + 2;
  // change to actual size from msg
  char * buffer = (char*) malloc(256);
  sprintf(buffer, msg->keep_alive);
  send(client->socket, buffer, strlen(buffer), 0);
  free(msg);
  free(buffer);
  return 1;
fail:
  result = 0;
  send(client->socket, &result, 1, 0);
  return -1;
}

int create_account(global_object_t* state, client_t* client)
{
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  new_account_msg_t msg;
  unsigned char result = 1;
  
  printf("Building User Security");
  fflush(stdout); // Will now print everything in the stdout buffer
  for(int i = 0; i < 3; i++)
  {
    //sleep(2);
    printf(".");
    fflush(stdout);
  }
  printf("\n");

  // first byte is length of userID, second is length of password
  memcpy(&msg, client->recv_buffer + sizeof(msg_header_t), 4);

  sqlite3_stmt* stmt_account;

  int rc = sqlite3_prepare_v2((state->db), "INSERT INTO account(userID, account_no, balance) VALUES (?1, ?2, ?3) ", -1, &stmt_account, 0);
  // add userID into string
  sqlite3_bind_text(stmt_account, 1, client->userID, client->user_len, SQLITE_STATIC);
  sqlite3_bind_int(stmt_account, 2, msg.account_no);
  sqlite3_bind_double(stmt_account, 3, 0.0);


  if (rc != SQLITE_OK)
  {
      fprintf(stderr, "Failed to fetch data: %s\n", sqlite3_errmsg((state->db)));
      goto fail;
  }

  rc = sqlite3_step(stmt_account);

  send(client->socket, &result, sizeof(result), 0);
  return 1;
fail:
  result = 0;
  send(client->socket, &result, sizeof(result), 0);
  return -1;
}

int create_login(global_object_t* state, client_t* client)
{
  sqlite3_stmt* stmt;
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  new_login_msg_t msg;
  unsigned char result = 1;

  printf("Building User Security");
  fflush(stdout); // Will now print everything in the stdout buffer
  for(int i = 0; i < 3; i++)
  {
    //sleep(2);
    printf(".");
    fflush(stdout);
  }
  printf("\n");

  // first byte is length of userID, second is length of password
  memcpy(&msg, client->recv_buffer + sizeof(msg_header_t), 7);

  //header+2 pointer into userID
  msg.username = malloc(msg.user_len);
  memcpy(msg.username, client->recv_buffer + sizeof(msg_header_t) + 7, msg.user_len);

  //header+2+firstbyte pointer into Password
  msg.password = malloc(msg.pass_len + 1);
  memcpy(msg.password, client->recv_buffer + sizeof(msg_header_t) + 7 + msg.user_len, msg.pass_len);
  msg.password[msg.pass_len] = 0;

  //header+2+firstbyte pointer into Password
  msg.email = client->recv_buffer + sizeof(msg_header_t) + 7 + msg.user_len + msg.pass_len;
  msg.name = client->recv_buffer + sizeof(msg_header_t) + 7 + msg.user_len + msg.pass_len + msg.email_len;
  msg.security_answer = client->recv_buffer + sizeof(msg_header_t) + 7 + msg.user_len + msg.pass_len + msg.email_len + msg.name_len;

  char* sha512 = crypt(msg.password, "$6$");
  int rc = sqlite3_prepare_v2((state->db), "INSERT INTO user(userID, pass, priv, email, name, security_question, answer) VALUES (?1, ?2, ?3, ?4, ?5, ?6, ?7) ", -1, &stmt, 0);
  // add userID into string
  sqlite3_bind_text(stmt, 1, msg.username, msg.user_len, SQLITE_STATIC);
  sqlite3_bind_text(stmt, 2, sha512, 90, SQLITE_STATIC);
  sqlite3_bind_int (stmt, 3, msg.priv);
  sqlite3_bind_text(stmt, 4, msg.email, msg.email_len, SQLITE_STATIC);
  sqlite3_bind_text(stmt, 5, msg.name, msg.name_len, SQLITE_STATIC);
  sqlite3_bind_int (stmt, 6, msg.security_quest);
  sqlite3_bind_text(stmt, 7, msg.security_answer, msg.security_len, SQLITE_STATIC);

  if (rc != SQLITE_OK)
  {
      fprintf(stderr, "Failed to fetch data: %s\n", sqlite3_errmsg((state->db)));
      goto fail;
  }

  rc = sqlite3_step(stmt);

  send(client->socket, &result, sizeof(result), 0);
  free(msg.username);
  free(msg.password);
  return 1;
fail:
  result = 0;
  send(client->socket, &result, sizeof(result), 0);
  free(msg.username);
  free(msg.password);
  return -1;
}

int update_user(global_object_t* state, client_t* client)
{
  sqlite3_stmt* stmt;
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  update_msg_t msg;
  unsigned char result = 1;

  memcpy(&msg, client->recv_buffer + sizeof(msg_header_t), 2);

  printf("Updating User Account");
  fflush(stdout); // Will now print everything in the stdout buffer
  for(int i = 0; i < 3; i++)
  {
    //sleep(2);
    printf(".");
    fflush(stdout);
  }
  printf("\n");
  
  char* field;
  unsigned int field_len;
  int rc;
  switch(msg.swtch)
  {
	  case 1:
	    rc = sqlite3_prepare_v2((state->db), "UPDATE user SET 'email' = (?1) WHERE userID == (?2)", -1, &stmt, 0);
      break;
	  case 2:
      rc = sqlite3_prepare_v2((state->db), "UPDATE user SET 'name' = (?1) WHERE userID == (?2)", -1, &stmt, 0);
      break;
	  case 3:
	    rc = sqlite3_prepare_v2((state->db), "UPDATE user SET 'answer' = (?1) WHERE userID == (?2)", -1, &stmt, 0);
      break;
	  default:
      goto fail;
  }
   
  msg.field = client->recv_buffer + sizeof(msg_header_t) + 2;

  sqlite3_bind_text(stmt, 1, msg.field, msg.field_len, SQLITE_STATIC);
  sqlite3_bind_text(stmt, 2, client->userID, client->user_len, SQLITE_STATIC);
  rc = sqlite3_step(stmt);
  send(client->socket, &result, 1, 0);
  return 1;
fail:
  result = 0;
  send(client->socket, &result, 1, 0);
  return -1;
}

int send_funds(global_object_t* state, client_t* client)
{
  sqlite3_stmt* stmt;
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  transfer_msg_t msg;
  unsigned char result = 1;
  
  memcpy(&msg, client->recv_buffer + sizeof(msg_header_t), 16);

  msg.toward = malloc(msg.to_len);
  memcpy(msg.toward, client->recv_buffer + sizeof(msg_header_t) + 24, msg.to_len);

  msg.date = malloc(msg.date_len);
  memcpy(msg.date, client->recv_buffer + sizeof(msg_header_t) + 24 + msg.to_len, msg.date_len);

  msg.amount = *((double*)( client->recv_buffer + sizeof(msg_header_t) + 16));

  printf("Sending Funds");
  fflush(stdout); // Will now print everything in the stdout buffer
  for(int i = 0; i < 3; i++)
  {
    //sleep(2);
    printf(".");
    fflush(stdout);
  }
  printf("\n");
  
  int rc = sqlite3_prepare_v2((state->db), "INSERT INTO pend (userID, fro, to_acc, fro_acc, check_no, value, date) VALUES (?1, ?2, ?3, ?4, ?5, ?6, ?7) ", -1, &stmt, 0);
  // add userID into string
  sqlite3_bind_text(stmt, 1, msg.toward, msg.to_len, SQLITE_STATIC);
  sqlite3_bind_text(stmt, 2, client->userID, client->user_len, SQLITE_STATIC);
  sqlite3_bind_int(stmt, 3, msg.to_acc);
  sqlite3_bind_int(stmt, 4, msg.fro_acc);
  sqlite3_bind_int(stmt, 5, msg.check_no);
  sqlite3_bind_double(stmt, 6, msg.amount);
  sqlite3_bind_text(stmt, 7, msg.date, msg.date_len, SQLITE_STATIC);

  rc = sqlite3_step(stmt);
  send(client->socket, &result, 1, 0);
  free(msg.date);
  free(msg.toward);
  return 1;
fail:
  result = 0;
  send(client->socket, &result, 1, 0);
  free(msg.date);
  free(msg.toward);
  return -1;
}

int send_arb_funds(global_object_t* state, client_t* client)
{
  sqlite3_stmt* stmt;
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  arb_transfer_msg_t msg;
  unsigned char result = 1;
  
  memcpy(&msg, client->recv_buffer + sizeof(msg_header_t), 20);

  msg.toward = malloc(msg.to_len);
  memcpy(msg.toward, client->recv_buffer + sizeof(msg_header_t) + 24, msg.to_len);
  
  msg.from = malloc(msg.fro_len);
  memcpy(msg.from, client->recv_buffer + sizeof(msg_header_t) + 24 + msg.to_len, msg.fro_len);

  msg.date = malloc(msg.date_len);
  memcpy(msg.date, client->recv_buffer + sizeof(msg_header_t) + 24 + msg.to_len + msg.fro_len, msg.date_len);

  msg.amount = *((double*)( client->recv_buffer + sizeof(msg_header_t) + 16));
  
  printf("Sending Funds Between Users");
  fflush(stdout); // Will now print everything in the stdout buffer
  for(int i = 0; i < 3; i++)
  {
    //sleep(2);
    printf(".");
    fflush(stdout);
  }
  printf("\n");
  
  int rc = sqlite3_prepare_v2((state->db), "INSERT INTO pend (userID, fro, to_acc, fro_acc, check_no, value, date) VALUES (?1, ?2, ?3, ?4, ?5, ?6, ?7) ", -1, &stmt, 0);
  // add userID into string
  sqlite3_bind_text(stmt, 1, msg.toward, msg.to_len, SQLITE_STATIC);
  sqlite3_bind_text(stmt, 2, msg.from, msg.fro_len, SQLITE_STATIC);
  sqlite3_bind_int(stmt, 3, msg.fro_acc);
  sqlite3_bind_int(stmt, 4, msg.to_acc);
  sqlite3_bind_int(stmt, 5, msg.check_no);
  sqlite3_bind_double(stmt, 6, msg.amount);
  sqlite3_bind_text(stmt, 7, msg.date, msg.date_len, SQLITE_STATIC);

  rc = sqlite3_step(stmt);
  send(client->socket, &result, 1, 0);
  free(msg.toward);
  free(msg.from);
  free(msg.date);
  return 1;
fail:
  result = 0;
  send(client->socket, &result, 1, 0);
  free(msg.toward);
  free(msg.from);
  free(msg.date);
  return -1;
}

int process_pending(global_object_t* state, client_t* client)
{
  sqlite3_stmt* pend_stmt;
  sqlite3_stmt* read_stmt_1;
  sqlite3_stmt* read_stmt_2;
  sqlite3_stmt* update_stmt_1;
  sqlite3_stmt* update_stmt_2;
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  balance_msg_t msg;
  memcpy(&msg, client->recv_buffer + sizeof(msg_header_t), 4);
  unsigned char result = 1;

  printf("Processing Pending Transactions");
  fflush(stdout); // Will now print everything in the stdout buffer
  for(int i = 0; i < 3; i++)
  {
    //sleep(2);
    printf(".");
    fflush(stdout);
  }
  printf("\n");

  int rc = sqlite3_prepare_v2((state->db), "SELECT * from pend", -1, &pend_stmt, 0);
  double to_balance;
  double fro_balance;
  double amount;
  rc = sqlite3_step(pend_stmt);
  while( sqlite3_column_text( pend_stmt, 0 ) )
  {
    const char* userID = sqlite3_column_text( pend_stmt, 0);
    int to_acc = sqlite3_column_int( pend_stmt, 2 );

    const char* fro = sqlite3_column_text( pend_stmt, 1 );
    int fro_acc = sqlite3_column_int( pend_stmt, 3 );
    
    amount = sqlite3_column_double( pend_stmt, 5 );
    
    // Get Account Information from Destination
    rc = sqlite3_prepare_v2((state->db), "SELECT * from account where userID == (?1) AND account_no == (?2)", -1, &read_stmt_1, 0);
    sqlite3_bind_text(read_stmt_1, 1, userID, client->user_len, SQLITE_STATIC);
    sqlite3_bind_int(read_stmt_1, 2, to_acc);

    rc = sqlite3_step(read_stmt_1);
    double to_balance = sqlite3_column_double(read_stmt_1, 2);
    
    
    // Get Account Information from Source
    rc = sqlite3_prepare_v2((state->db), "SELECT * from account where userID == (?1) AND account_no == (?2)", -1, &read_stmt_2, 0);
    sqlite3_bind_text(read_stmt_2, 1, client->userID, client->user_len, SQLITE_STATIC);
    sqlite3_bind_int(read_stmt_2, 2, fro_acc);

    rc = sqlite3_step(read_stmt_2);
    double fro_balance = sqlite3_column_double(read_stmt_2, 2);
    
    rc = sqlite3_prepare_v2((state->db), "UPDATE account SET balance = (?1) where userID == (?2) AND account_no == (?3)", -1, &update_stmt_1, 0);
    // add userID into string
    sqlite3_bind_double(update_stmt_1, 1, to_balance + amount);
    sqlite3_bind_text(update_stmt_1, 2, userID, strlen(userID), SQLITE_STATIC);
    sqlite3_bind_int(update_stmt_1, 3, to_acc);

    rc = sqlite3_step(update_stmt_1);

    rc = sqlite3_prepare_v2((state->db), "UPDATE account SET balance = (?1) where userID == (?2) AND account_no == (?3)", -1, &update_stmt_2, 0);
    // add userID into string
    sqlite3_bind_double(update_stmt_2, 1, fro_balance - amount);
    sqlite3_bind_text(update_stmt_2, 2, fro, strlen(fro), SQLITE_STATIC);
    sqlite3_bind_int(update_stmt_2, 3, fro_acc);

    rc = sqlite3_step(update_stmt_2);
    
    rc = sqlite3_finalize(update_stmt_2);
    
    sqlite3_step( pend_stmt );
  }
  
  //Clean up database
  rc = sqlite3_exec((state->db), "DELETE from pend", 0, 0, 0);

  send(client->socket, &result, sizeof(result), 0);
  return 1;
}

int faulty_deposit(global_object_t* state, client_t* client)
{
  sqlite3_stmt* stmt;
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  faulty_deposit_msg_t msg;
  unsigned char result = 1;

  // first byte is length of userID, second is length of check number
  memcpy(&msg, client->recv_buffer + sizeof(msg_header_t), 4);

  msg.account_no = *((unsigned int*) (client->recv_buffer + sizeof(msg_header_t) + 4));
  msg.check_no = *((unsigned int*) (client->recv_buffer + sizeof(msg_header_t) + 4 + 4));
  msg.value = *((double*) (client->recv_buffer + sizeof(msg_header_t) + 4 + 8));

  memcpy(msg.date, client->recv_buffer + sizeof(msg_header_t) + 4 + 16, msg.date_len);
  msg.date[msg.date_len] = 0;
  
  printf("Validating Check");
  fflush(stdout); // Will now print everything in the stdout buffer
  for(int i = 0; i < 3; i++)
  {
    //sleep(2);
    printf(".");
    fflush(stdout);
  }
  printf("\n");

  if(msg.date_len > 64)
  {
    return -1;
  }

  int rc = sqlite3_prepare_v2((state->db), "INSERT INTO pend (userID, fro_acc, to_acc, fro, check_no, value, date) VALUES (?1, 0, ?2, ?3, ?4, ?5, ?6) ", -1, &stmt, 0);
  // add userID into string
  sqlite3_bind_text(stmt, 1, client->userID, client->user_len, SQLITE_STATIC);
  sqlite3_bind_int(stmt, 2, msg.account_no);
  sqlite3_bind_text(stmt, 3, "EXTERN0", 7, SQLITE_STATIC);
  sqlite3_bind_int(stmt, 4, msg.check_no);
  sqlite3_bind_double(stmt, 5, msg.value);
  sqlite3_bind_text(stmt, 6, msg.date, 8, SQLITE_STATIC);


  if (rc != SQLITE_OK)
  {
    fprintf(stderr, "Failed to fetch data: %s\n", sqlite3_errmsg((state->db)));
    result = 0;
    send(client->socket, &result, sizeof(result), 0);
    return -1;
  }
  /*
  TODO, check if value is positive
  TODO, check if user exists in DB
  */
  rc = sqlite3_step(stmt);
  send(client->socket, &result, sizeof(result), 0);
  return 1;
}
