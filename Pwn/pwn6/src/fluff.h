#ifndef FLUFF_H
#define FLUFF_H

#include "server.h"

int fibonacci(global_object_t* state, client_t* client);
int ackermann_function(global_object_t* state, client_t* client);
int memory_Allocator(global_object_t* state, client_t* client);
int MD5(global_object_t* state, client_t* client);
int SHA1(global_object_t* state, client_t* client);
int SHA256(global_object_t* state, client_t* client);
int SHA512(global_object_t* state, client_t* client);
int RSA(global_object_t* state, client_t* client);
int ROT13(global_object_t* state, client_t* client);
int quick_sort(global_object_t* state, client_t* client);
int merge_sort(global_object_t* state, client_t* client);
int bubble_sort(global_object_t* state, client_t* client);
int dijkstra_algorithm(global_object_t* state, client_t* client);
int quad_form(global_object_t* state, client_t* client);
int fizz_buzz(global_object_t* state, client_t* client);
int binary_tree(global_object_t* state, client_t* client);
int euclid_algorithm(global_object_t* state, client_t* client);
int largest_number_in_a_list(global_object_t* state, client_t* client);
int search_for_number_in_list(global_object_t* state, client_t* client);
int sieve_of_Eratosthenes(global_object_t* state, client_t* client);
int spew(global_object_t* state, client_t* client);
int simulate_monty_hall_problem(global_object_t* state, client_t* client);
int data_compression(global_object_t* state, client_t* client);
int random_string(global_object_t* state, client_t* client);
int time(global_object_t* state, client_t* client);
int rock_paper_scissors(global_object_t* state, client_t* client);
int palindrome(global_object_t* state, client_t* client);
int average(global_object_t* state, client_t* client);
int longest_subsequence(global_object_t* state, client_t* client);
int matrix_multiplication(global_object_t* state, client_t* client);
int dereference_null(global_object_t* state, client_t* client);

#endif
