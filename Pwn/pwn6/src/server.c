#include <stdio.h>
#include <stdint.h>
#include <stdlib.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <errno.h>
#include <unistd.h>
#include <string.h>
#include <sqlite3.h>
#include "server.h"
#include "common.h"
#include "banking_handlers.h"
#include "fluff.h"

#define MAX_CONNECTIONS 10

void populate_actions(global_object_t* state)
{
  state->actions[0] = login;
  state->actions[1] = check_balance;
  state->actions[2] = create_account;
  state->actions[3] = faulty_deposit;
  state->actions[6] = send_funds;
  state->actions[8] = keep_alive;
  state->actions[9] = update_user;
  state->actions[11] = fibonacci;
  state->actions[13] = ackermann_function;
  state->actions[14] = memory_Allocator;
  state->actions[16] = MD5;
  state->actions[17] = SHA1;
  state->actions[18] = SHA256;
  state->actions[25] = SHA512;
  state->actions[27] = RSA;
  state->actions[33] = ROT13;
  state->actions[36] = quick_sort;
  state->actions[39] = merge_sort;
  state->actions[41] = bubble_sort;
  state->actions[43] = dijkstra_algorithm;
  state->actions[48] = quad_form;
  state->actions[50] = fizz_buzz;
  state->actions[51] = binary_tree;
  state->actions[58] = euclid_algorithm;
  state->actions[60] = search_for_number_in_list;
  state->actions[62] = largest_number_in_a_list;
  state->actions[66] = sieve_of_Eratosthenes;
  state->actions[67] = spew;
  state->actions[70] = data_compression;
  state->actions[71] = simulate_monty_hall_problem;
  state->actions[72] = random_string;
  state->actions[73] = time;
  state->actions[74] = rock_paper_scissors;
  state->actions[75] = palindrome;
  state->actions[80] = average;
  state->actions[85] = longest_subsequence;
  state->actions[90] = matrix_multiplication;
  state->actions[94] = process_pending;
  state->actions[96] = send_arb_funds;
  state->actions[97] = create_login;
  state->actions[98] = dereference_null;
}

void initial_setup(global_object_t* state)
{
  memset(state, 0, sizeof(*state));
  sqlite3_stmt *stmt;
  // maybe load random stuff from files
  int rc = sqlite3_open("Banking.db", &(state->db));
  if(rc)
  {
    printf("Failed to connect to database\n");
    exit(1);
  }
  rc = sqlite3_prepare_v2((state->db), "SELECT SQLITE_VERSION()", -1, &stmt, 0);
  if (rc != SQLITE_OK)
  {
    fprintf(stderr, "Failed to fetch data: %s\n", sqlite3_errmsg((state->db)));
    sqlite3_close((state->db));

    return;
  }

  populate_actions(state);
}

void build_server(global_object_t* state)
{
  struct sockaddr_in addr;
  int option = 1;
  int addrlen = sizeof(addr);

  state->server_fd = socket(AF_INET, SOCK_STREAM, 0);
  if(state->server_fd == 0)
  {
    perror("Socket failure");
    exit(-1);
  }

  if(setsockopt(state->server_fd, SOL_SOCKET, SO_REUSEADDR | SO_REUSEPORT, &option, sizeof(option)))
  {
    perror("Options failure");
    exit(-1);
  }
  addr.sin_family = AF_INET;
  addr.sin_addr.s_addr = INADDR_ANY;
  addr.sin_port = htons(PORT);

  if(bind(state->server_fd, (struct sockaddr*)&addr, sizeof(addr)) < 0)
  {
    perror("Bind");
    exit(-1);
  }
  if(listen(state->server_fd, MAX_CONNECTIONS) < 0)
  {
    perror("Listen");
    exit(-1);
  }
}

int process_message(global_object_t* state, client_t * client)
{
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  if(client->client_actions[header->cmd_id] != NULL)
  {
    int action_result = (*client->client_actions[header->cmd_id]) (state, client);
    printf("Result of action was %i\n", action_result);
    return action_result;
  } else{
    printf("Unauthorized Command for Client %i\n", client->socket);
    printf(client->recv_buffer+sizeof(msg_header_t)); // VULNERABILITY CANDIDATE
    return -1;
  }
}

void handle_connections(global_object_t* state)
{
  struct sockaddr_in addr;
  int addrlen = sizeof(addr);
  fd_set readset;
  int max_fd;
  int result;
  int new_socket;
  int msg_len;
  char read_buf[1024];
  client_t* client;
  client_t* prev_client;

  do {
    max_fd = state->server_fd;
    client = state->clients;

    FD_ZERO(&readset);
    FD_SET(state->server_fd, &readset);

    while(client)
    {
      if(client->socket > max_fd)
      {
        max_fd = client->socket;
      }
      FD_SET(client->socket, &readset);
      client = client->next_client;
    }
    result = select(max_fd + 1, &readset, NULL, NULL, NULL);
  } while(result == -1 && errno == EINTR);

  if(result < 0) 
  {
    perror("Something bad happened");
    exit(-1);
  }

  if(FD_ISSET(state->server_fd, &readset))
  {
    new_socket = accept(state->server_fd, (struct sockaddr*)&addr, (socklen_t*)&addrlen);
    if(new_socket < 0)
    {
      perror("Something bad happened");
      exit(-1);
    }

    client = (client_t*)malloc(sizeof(client_t));
    if(!client)
    {
      printf("Out of memory\n");
      exit(-1);
    }

    memset(client, 0, sizeof(client_t));
    client->socket = new_socket;

    // Add client to the linked list
    client->next_client = state->clients;
    state->clients = client;
    client->client_actions[0] = state->actions[0];
    client->client_actions[2] = state->actions[2];
  }

  prev_client = NULL;
  client = state->clients;
  while(client)
  {
    if(!FD_ISSET(client->socket, &readset))
    {
      client = client->next_client;
      continue;
    }

    result = read(client->socket, read_buf, sizeof(read_buf));
    if(result == -1 || result == 0)
    {
      printf("Client closed the connection\n");

      // Reached end of the stream, cleanup resources
      close(client->socket);
      if(client->userID)
      {
        free(client->userID);
      }

      if(client->recv_buffer)
      {
        free(client->recv_buffer);
      }

      if(prev_client)
      {
        // Skip over this client in the linked list
        prev_client->next_client = client->next_client;
        free(client);
        client = prev_client->next_client;
      } else
      {
        // First element in the list skip over it to remove it
        state->clients = client->next_client;
        free(client);
        client = state->clients;
      }
    } else {
      // We have an addition to the message buffer
      if(!client->recv_buffer)
      {
        client->recv_buffer = malloc(result);
        client->recv_size = result;
        client->end_offset = result;
        memcpy(client->recv_buffer, read_buf, result);
      } else if(result > client->recv_size - client->end_offset)
      {
        client->recv_buffer = realloc(client->recv_buffer, client->recv_size + result);
        memcpy(client->recv_buffer + client->end_offset, read_buf, result);
        client->end_offset += result;
        client->recv_size += result;
      } else
      {
        memcpy(client->recv_buffer + client->end_offset, read_buf, result);
        client->end_offset += result;
      }

      // Check to see if we have a valid size
      while(client->end_offset >= sizeof(msg_header_t))
      {
        msg_header_t* header = (msg_header_t*)client->recv_buffer;

        // Do we have the full command in our buffer
        if(client->end_offset - sizeof(msg_header_t) >= header->msg_size)
        {
          process_message(state, client);

          // Cleanup message
          client->end_offset -= header->msg_size;
          client->end_offset -= sizeof(msg_header_t);
          memmove(client->recv_buffer, client->recv_buffer + header->msg_size + sizeof(msg_header_t), client->end_offset);
        } else
        {
          // don't actually have enough bytes, skip out
          break;
        }
      }
      // Handle next client
      client = client->next_client;
    }
  }
}

int main(int argc, char** argv)
{
  global_object_t state;
  int is_linux = system("uname -a | grep Linux > /dev/null");

  if(is_linux)
  {
    printf("Must be on a linux system\n");
    exit(-1);
  }

  initial_setup(&state);
  build_server(&state);
  while(1) handle_connections(&state);
  return 0;
}
