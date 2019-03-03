#ifndef MD5_H
#define MD5_H

#include <stdint.h>

void md5(uint8_t *initial_msg, size_t initial_len);
void get_md5hash(uint32_t hash[4]);

#endif
