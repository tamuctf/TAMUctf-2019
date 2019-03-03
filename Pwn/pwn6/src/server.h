#ifndef SERVER_H
#define SERVER_H

#include <sqlite3.h>

struct global_object_t;
struct client_t;

typedef int (*Handler)(struct global_object_t*, struct client_t*);

typedef struct global_object_t
{
  int server_fd;
  struct client_t* clients;
  sqlite3 * db;
  Handler actions[100];
} global_object_t;

typedef struct client_t
{
  int socket;
  char* recv_buffer;
  char* userID;
  unsigned char user_len;
  int recv_size;
  int end_offset;
  Handler client_actions[100];
  struct client_t* next_client;
} client_t;

#endif
