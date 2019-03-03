#ifndef BANKING_HANDLERS_H
#define BANKING_HANDLERS_H

#include "server.h"

int login(global_object_t* state, client_t* client);
int check_balance(global_object_t* state, client_t* client);
int deposit(global_object_t* state, client_t* client);
int keep_alive(global_object_t* state, client_t* client);
int create_account(global_object_t* state, client_t* client);
int create_login(global_object_t* state, client_t* client);
int update_user(global_object_t* state, client_t* client);
int send_funds(global_object_t* state, client_t* client);
int send_arb_funds(global_object_t* state, client_t* client);
int process_pending(global_object_t* state, client_t* client);
int faulty_deposit(global_object_t* state, client_t* client);

#endif
