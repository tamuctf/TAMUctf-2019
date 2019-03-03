#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>
#include <string.h>
#include <unistd.h>
#include <fcntl.h>
#include "fluff.h"
#include "server.h"
#include "common.h"
#include "md5.h"

int fibonacci(global_object_t* state, client_t* client)
{
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  fib_msg_t msg;

  memcpy(&msg, client->recv_buffer + sizeof(msg_header_t), 4);
  int first = 0, second = 1, next, c;

  for ( c = 0 ; c < msg.n ; c++ )
  {
    if ( c <= 1 )
      next = c;
    else
    {
      next = first + second;
      first = second;
      second = next;
    }
  }
  if(next > 0x1d4){
    printf("%d\n",next);
    return 1;
  }
}

int ackermann(int* cache, int m_bits, int n_bits, int m, int n)
{
  int idx, res;
  if (!m) return n + 1;

  if (n >= 1 << n_bits)
  {
    printf("%d, %d\n", m, n);
    idx = 0;
  } else
  {
    idx = (m << n_bits) + n;
    if (cache[idx]) return cache[idx];
  }

  if (!n) res = ackermann(cache, m_bits, n_bits, m - 1, 1);
  else    res = ackermann(cache, m_bits, n_bits, m - 1, ackermann(cache, m_bits, n_bits, m, n - 1));

  if (idx) cache[idx] = res;
  return res;
}

int ackermann_function(global_object_t* state, client_t* client)
{
  // https://rosettacode.org/wiki/Ackermann_function#C

  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  ackermann_function_msg_t* msg = (ackermann_function_msg_t*)(client->recv_buffer + sizeof(msg_header_t));
  int cache_size = sizeof(int) * (1 << msg->m_bits + msg->n_bits);
  int* cache = (int*)malloc(cache_size);
  int result = ackermann(cache, msg->m_bits, msg->n_bits, msg->m, msg->n);
  memset(cache, 0, cache_size);

  write(client->socket, &result, sizeof(result));

  // move cache to global state for uaf vuln?
  free(cache);
  return 1;
}


int memory_Allocator(global_object_t* state, client_t* client)
{
  return 1;
}

int MD5(global_object_t* state, client_t* client)
{
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  uint32_t hash[4];
  md5(client->recv_buffer + sizeof(msg_header_t), header->msg_size);

  get_md5hash(hash);

  write(client->socket, hash, sizeof(hash));
  return 1;
}

int SHA1(global_object_t* state, client_t* client)
{
  return 1;
}

int SHA256(global_object_t* state, client_t* client)
{
  return 1;
}

int SHA512(global_object_t* state, client_t* client)
{
  return 1;
}

int RSA(global_object_t* state, client_t* client)
{
  return 1;
}

int ROT13(global_object_t* state, client_t* client)
{
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  char* str = client->recv_buffer + sizeof(msg_header_t);

  int i =0;
  // loop until str itself is not NULL and str[i] is not zero
  for(i=0;str && i < header->msg_size; ++i) // ++i is a pre-increment
  {
    if(str[i] >= 'a' && (str[i]+13) <='z')
    {
      str[i] = str[i]+13;       // modifying str in place
    }
  }
  write(client->socket, &header->msg_size, sizeof(header->msg_size));
  write(client->socket, str, header->msg_size);
  return 1;
}

int quick_sort(global_object_t* state, client_t* client)
{
  return 1;
}

int merge_sort(global_object_t* state, client_t* client)
{
  return 1;
}

int bubble_sort(global_object_t* state, client_t* client)
{
  return 1;
}

int dijkstra_algorithm(global_object_t* state, client_t* client)
{
  return 1;
}

int quad_form(global_object_t* state, client_t* client)
{
  return 1;
}

int fizz_buzz(global_object_t* state, client_t* client)
{
  return 1;
}

int binary_tree(global_object_t* state, client_t* client)
{
  return 1;
}

int euclid_algorithm(global_object_t* state, client_t* client)
{
  return 1;
}

int largest_number_in_a_list(global_object_t* state, client_t* client)
{
  return 1;
}

int search_for_number_in_list(global_object_t* state, client_t* client)
{
  return 1;
}

//Sieve of Eratosthenes/Trial division prime checks
int sieve_of_Eratosthenes(global_object_t* state, client_t* client)
{
  return 1;
}

//Spew /dev/urandom back
int spew(global_object_t* state, client_t* client)
{
  msg_header_t* header = (msg_header_t*)client->recv_buffer;
  int length = *(int*)(client->recv_buffer + sizeof(msg_header_t));
  int rndfd = open("/dev/urandom", O_RDONLY);
  char* buffer = malloc(length);

  if(rndfd < 0)
  {
    printf("Failed to open file\n");
    exit(-1);
  }

  read(rndfd, buffer, length);
  write(client->socket, buffer, length);
  return 1;
}

int simulate_monty_hall_problem(global_object_t* state, client_t* client)
{
  return 1;
}

int data_compression(global_object_t* state, client_t* client)
{
  return 1;
}

int random_string(global_object_t* state, client_t* client)
{
  #include "words.h"

  int rnd = rand() % (sizeof(word_list) / sizeof(char*));
  int size = strlen(word_list[rnd]);

  write(client->socket, &size, sizeof(size));
  write(client->socket, word_list[rnd], size);
  return 1;
}

//Time/Date
int time(global_object_t* state, client_t* client)
{
  return 1;
}

int rock_paper_scissors(global_object_t* state, client_t* client)
{
  char* message = "\x1fYou lost at rock paper scissors";
  write(client->socket, message, strlen(message));
  return 1;
}

int palindrome(global_object_t* state, client_t* client)
{
  return 1;
}

int average(global_object_t* state, client_t* client)
{
  return 1;
}

int longest_subsequence(global_object_t* state, client_t* client)
{
  return 1;
}

int matrix_multiplication(global_object_t* state, client_t* client)
{
  return 1;
}

//Dereference null/non-exploitable crashes
int dereference_null(global_object_t* state, client_t* client)
{
  char* c = 0;
  *c = 50;
  return 1;
}
